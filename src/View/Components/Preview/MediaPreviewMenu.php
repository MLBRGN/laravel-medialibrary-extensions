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

    public function __construct(
        ?string $id,
        public ?string $mediaManagerId,
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
    ) {
        parent::__construct($id);

        $this->mediaManagerId = $mediaManagerId ?? $this->id;

        // Ensure instanceId is derived from the mediaManagerId (the parent manager's identity)
        $this->instanceId = InstanceManager::getInstanceId($this->mediaManagerId);

        $this->options = $options;

        $this->resolveConfig();
    }

    public function render(): View
    {
        return $this->renderView('preview.media-preview-menu', $this->getConfig('frontendTheme'));
    }
}
