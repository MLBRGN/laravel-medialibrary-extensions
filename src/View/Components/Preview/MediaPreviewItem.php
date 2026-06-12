<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Preview;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPreviewItem extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public ?string $componentToRender;

    public ?string $mediumType;

    public function __construct(
        ?string $id,
        public ?string $mediaManagerId = null,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public $medium,
        public array $collections = [],
        array $options = [],
        public int $loopIndex = 0,
        public Media|TemporaryUpload|null $singleMedia = null, // when provided, skip collection lookups and use this medium
        public bool $multiple = false,
        public bool $disabled = false,
        public bool $readonly = false,
        public bool $selectable = false,
        public string $instanceId = '',
        public ?string $dataSource = null,
    ) {
        parent::__construct($id);

        $this->mediaManagerId = $mediaManagerId ?? $this->originalId;

        // Ensure instanceId is derived from the mediaManagerId (the parent manager's identity)
        $this->instanceId = \Mlbrgn\MediaLibraryExtensions\Support\InstanceManager::getInstanceId($this->mediaManagerId);

        $this->options = $options;

        $componentMap = [
            'youtube-video' => 'mle-video-youtube',
            'document' => 'mle-document',
            'video' => 'mle-video',
            'audio' => 'mle-audio',
            'image' => 'mle-image-responsive',
        ];

        $this->mediumType = getMediaType($medium);
        $this->componentToRender = $componentMap[$this->mediumType] ?? null;

        $this->resolveConfig();
    }

    public function render(): View
    {
        return $this->renderView('preview.media-preview-item', $this->getConfig('frontendTheme'));
    }
}
