<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMimeTypes;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseMediaComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// TODO $dataSource?
class UploadForm extends BaseMediaComponent
{
    use InteractsWithMimeTypes;
    use InteractsWithOptionsAndConfig;

    protected array $fileRequirements = [];

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a model implementing HasMedia or its class name
        public Media|TemporaryUpload|null $singleMedia = null,
        public array $collections = [],
        array $options = [],
        public bool $multiple = false,
        public ?bool $readonly = false,
        public ?bool $disabled = false,
        public string $instanceId = '',
        public ?string $dataSource = 'default',
        ?string $clientToken = null,
    ) {
        parent::__construct($id, $this->modelOrClassName, $dataSource);

        $this->options = $options;

        if (empty($instanceId)) {
            $this->instanceId = InstanceManager::getInstanceId($this->id);
        } else {
            $this->instanceId = $instanceId;
        }

        if ($clientToken) {
            $this->clientToken = $clientToken;
        }

        $mimeData = $this->resolveAllowedMimeTypes();

        $this->resolveConfig([
            ...$mimeData,
        ]);

        $this->fileRequirements = [
            'max_file_size' => config('medialibrary-extensions.max_upload_size'),
            'max_width' => config('medialibrary-extensions.max_image_width'),
            'max_height' => config('medialibrary-extensions.max_image_height'),
            'min_width' => config('medialibrary-extensions.min_image_width'),
            'min_height' => config('medialibrary-extensions.min_image_height'),
        ];

        $this->totalMediaCount = $this->mediaService->countMediaInCollections(
            $this->resolvedModel,
            $this->collections,
            $this->instanceId,
            $this->clientToken,
            $this->dataSource
        );
    }

    protected function domIdSuffix(): string
    {
        return 'upload-form';
    }

    public function getSupportedFilesSummary(): string
    {
        $parts = [];

        if ($formats = $this->getConfig('allowedMimeTypesHuman')) {
            $parts[] = $formats;
        }

        if ($size = $this->getMaximumFileSize()) {
            $parts[] = __('medialibrary-extensions::messages.up_to_size', [
                'size' => $size,
            ]);
        }

        if ($dimensions = $this->getDimensionSummary()) {
            $parts[] = $dimensions;
        }

        return implode(' • ', $parts);
    }

    protected function getMaximumFileSize(): ?string
    {
        return $this->fileRequirements['max_file_size']
            ? mle_human_filesize($this->fileRequirements['max_file_size'])
            : null;
    }

    protected function getDimensionSummary(): ?string
    {
        $minWidth  = $this->fileRequirements['min_width'];
        $minHeight = $this->fileRequirements['min_height'];
        $maxWidth  = $this->fileRequirements['max_width'];
        $maxHeight = $this->fileRequirements['max_height'];

        if ($minWidth && $minHeight && $maxWidth && $maxHeight) {
            return __('medialibrary-extensions::messages.dimension_range', [
                'min_width' => $minWidth,
                'min_height' => $minHeight,
                'max_width' => $maxWidth,
                'max_height' => $maxHeight,
            ]);
        }

        if ($maxWidth || $maxHeight) {
            return __('medialibrary-extensions::messages.up_to_dimensions', [
                'width' => $maxWidth ?? '∞',
                'height' => $maxHeight ?? '∞',
            ]);
        }

        if ($minWidth || $minHeight) {
            return __('medialibrary-extensions::messages.at_least_dimensions', [
                'width' => $minWidth ?? 0,
                'height' => $minHeight ?? 0,
            ]);
        }

        return null;
    }

    public function render(): View
    {
        return $this->renderView('upload-form', $this->getConfig('theme'), true);
    }
}
