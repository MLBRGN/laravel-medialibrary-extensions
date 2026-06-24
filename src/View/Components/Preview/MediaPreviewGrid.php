<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Preview;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseMediaComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPreviewGrid extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;

    public function __construct(
        ?string $id,
        public ?string $mediaManagerDomId,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public array $collections = [],
        array $options = [],
        public Media|TemporaryUpload|null $singleMedia = null, // when provided, skip collection lookups and use this medium
        public bool $disabled = false,
        public bool $selectable = false,
        public bool $readonly = false,
        public bool $multiple = false,
        public string $instanceId = '',
        public ?string $dataSource = 'default',
        ?string $clientToken = null,
    ) {
        parent::__construct($id, $this->modelOrClassName, $dataSource);

        $this->mediaManagerDomId = $mediaManagerDomId ?? $this->id;

        // Ensure instanceId is derived from the mediaManagerDomId (the parent manager's identity)
        if (empty($instanceId)) {
            $this->instanceId = InstanceManager::getInstanceId($this->mediaManagerDomId);
        } else {
            $this->instanceId = $instanceId;
        }

        $this->options = $options;

        $this->resolveConfig([
            'temporaryUploadMode' => $this->temporaryUploadMode,
        ]);
    }

    public function render(): View
    {
        return $this->renderView('preview.media-preview-grid', $this->getConfig('frontendTheme'));
    }
}
