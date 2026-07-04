<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Preview;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
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
        public ?string $dataSource = 'default',
        ?string $clientToken = null,
    ) {
        parent::__construct($id);

        $this->instanceId = InstanceManager::getInstanceId($this->id);

        if ($clientToken) {
            $this->clientToken = $clientToken;
        }

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

    protected function domIdSuffix(): string
    {
        return 'media-preview-item-'.$this->loopIndex;
    }

    public function render(): View
    {
        return $this->renderView('preview.media-preview-item', $this->getConfig('theme'));
    }
}
