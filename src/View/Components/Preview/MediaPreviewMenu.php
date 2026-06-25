<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Preview;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPreviewMenu extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    /** Identity of the parent MediaManager (logical ID, not suffixed) */
    public string $mediaManagerId;

    /** Identity of the parent MediaManager (DOM ID, potentially suffixed) */
    public string $mediaManagerDomId;

    public function __construct(
        ?string $id,
        ?string $mediaManagerDomId,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public $medium,
        public array $collections = [],
        array $options = [],
        public Media|TemporaryUpload|null $singleMedia = null, // when provided, skip collection lookups and use this medium
        public bool $disabled = false,
        public bool $selectable = false,
        public bool $readonly = false,
        public bool $multiple = false,
        public string $instanceId = '',
        public ?string $dataSource = 'default',
        ?string $mediaManagerId = null,
    ) {
        parent::__construct($id);

        $this->mediaManagerId = $mediaManagerId ?? $this->id;
        $this->mediaManagerDomId = $mediaManagerDomId ?? $this->getDomId();

        // Ensure instanceId is derived from the mediaManagerId (the parent manager's stable identity)
        $this->instanceId = InstanceManager::getInstanceId($this->mediaManagerId);

        $this->options = $options;

        $this->resolveConfig();
    }

    protected function domIdSuffix(): string {
        return 'media-preview-menu';
    }

    public function render(): View
    {
        return $this->renderView('preview.media-preview-menu', $this->getConfig('frontendTheme'));
    }
}
