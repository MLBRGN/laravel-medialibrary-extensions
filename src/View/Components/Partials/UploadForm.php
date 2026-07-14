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

    public function getFileRequirementsSummary(): string
    {
        $requirements = [];

        if ($this->fileRequirements['max_file_size']) {
            $requirements[] = sprintf(
                'Maximum file size: %s',
                mle_human_filesize($this->fileRequirements['max_file_size'])
            );
        }

        if ($this->fileRequirements['max_width'] || $this->fileRequirements['max_height']) {
            $requirements[] = sprintf(
                'Maximum dimensions: %s × %s px',
                $this->fileRequirements['max_width'] ?? '∞',
                $this->fileRequirements['max_height'] ?? '∞'
            );
        }

        if ($this->fileRequirements['min_width'] || $this->fileRequirements['min_height']) {
            $requirements[] = sprintf(
                'Minimum dimensions: %s × %s px',
                $this->fileRequirements['min_width'] ?? 0,
                $this->fileRequirements['min_height'] ?? 0
            );
        }

        return implode("\n", $requirements);
    }

//    protected function formatBytes(int $bytes)
//    {
//        if ($bytes > 1024 * 1024) {
//            return round($bytes / 1024 / 1024, 2).' MB';
//        } elseif ($bytes > 1024) {
//            return round($bytes / 1024, 2).' KB';
//        }
//
//        return $bytes . ' B';
//    }

    public function render(): View
    {
        return $this->renderView('upload-form', $this->getConfig('theme'), true);
    }
}
