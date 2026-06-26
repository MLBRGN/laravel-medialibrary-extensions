<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaModal extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,
        public ?array $collections,
        public ?string $title,// TODO do i want this?
        public Media|TemporaryUpload|null $singleMedia = null, // when provided, skip collection lookups and use this medium
        array $options = [],
        public bool $videoAutoPlay = true,
        string $instanceId = '',
        public ?string $dataSource = 'default',
        ?string $clientToken = null,
    ) {
        parent::__construct($id, $this->modelOrClassName, $dataSource);

        if ($instanceId) {
            $this->instanceId = $instanceId;
        }

        if ($clientToken) {
            $this->clientToken = $clientToken;
        }
        $this->options = $options;

        // merge into config
        $this->resolveConfig();
    }

    protected function domIdSuffix(): string
    {
        return 'mod';
    }

    public function render(): View
    {
        return $this->renderView('media-modal', $this->getConfig('frontendTheme'));
    }
}
