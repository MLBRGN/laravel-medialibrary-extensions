<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaCounter;
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
        string $id,
        public mixed $modelReference,// either a model implementing HasMediaExtended or its class name
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
        parent::__construct($id, $this->modelReference, $dataSource);

        $this->options = $options;
        
        if ($this->multiple) {
            $maxFromOptions = $this->getOption('maxMediaCount', null);
            $this->maxMediaCount = (int) ($maxFromOptions ?? config('medialibrary-extensions.max_items_in_shared_media_collections', 10));
        } else {
            $this->maxMediaCount = 1;
            $this->setOption('maxMediaCount', 1);
        }

        $this->minMediaCount = (int) $this->getOption('minMediaCount', 0);
        if ($this->minMediaCount > $this->maxMediaCount) {
            $this->minMediaCount = $this->maxMediaCount;
            $this->setOption('minMediaCount', $this->minMediaCount);
        }

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

        $configured = config('medialibrary-extensions.max_upload_size');
        $server = mle_server_upload_limit();

        // TODO move the warning out of this component (or only emit it in local environments)
        if ($configured > $server) {
            logger()->warning(
                'The configured max_upload_size exceeds PHP upload_max_filesize/post_max_size. The effective upload limit is '.mle_human_filesize($server).'.'
            );
        }

        $this->fileRequirements = [
            'configured_max_file_size' => $configured,
            'server_max_file_size' => $server,
            'max_width' => config('medialibrary-extensions.max_image_width'),
            'max_height' => config('medialibrary-extensions.max_image_height'),
            'min_width' => config('medialibrary-extensions.min_image_width'),
            'min_height' => config('medialibrary-extensions.min_image_height'),
        ];

        $mediaCounter = app(MediaCounter::class);
        $this->totalMediaCount = $mediaCounter->countMediaInCollections(
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

        $min = (int) $this->getConfig('minMediaCount');
        $max = (int) $this->getConfig('maxMediaCount');
        $required = (bool) $this->getConfig('required');

        if ($required || $min > 0) {
            if ($min > 1) {
                $parts[] = __('medialibrary-extensions::messages.this_collection_requires_at_least_:items_items', ['items' => $min]);
            } else {
                $parts[] = $this->multiple
                    ? __('medialibrary-extensions::messages.at_least_one_medium_required')
                    : __('medialibrary-extensions::messages.one_medium_required');
            }
        }

        if ($max > 1 || ($max > 0 && $this->multiple)) {
            $parts[] = __('medialibrary-extensions::messages.this_collection_can_contain_up_to_:items_items', ['items' => $max]);
        }

        return implode(' • ', $parts);
    }

    //    protected function getMaximumFileSize(): ?string
    //    {
    //        return $this->fileRequirements['max_file_size']
    //            ? mle_human_filesize($this->fileRequirements['max_file_size'])
    //            : null;
    //    }

    public function isLimitedByServerConfiguration(): bool
    {
        return $this->fileRequirements['server_max_file_size']
            < $this->fileRequirements['configured_max_file_size'];
    }

    public function getServerUploadLimit(): string
    {
        return mle_human_filesize(
            $this->fileRequirements['server_max_file_size']
        );
    }

    protected function getMaximumFileSize(): ?string
    {
        $configured = $this->fileRequirements['configured_max_file_size'];
        $server = $this->fileRequirements['server_max_file_size'];

        return mle_human_filesize(min($configured, $server));
    }

    protected function getDimensionSummary(): ?string
    {
        $minWidth = $this->fileRequirements['min_width'];
        $minHeight = $this->fileRequirements['min_height'];
        $maxWidth = $this->fileRequirements['max_width'];
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
