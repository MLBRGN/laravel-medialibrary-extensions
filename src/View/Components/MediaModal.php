<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaModal extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,
        //        public ?string $mediaCollection,
        public ?array $collections,
        public ?string $title,// TODO do i want this?
        public Media|TemporaryUpload|null $singleMedia = null, // when provided, skip collection lookups and use this medium
        array $options = [],
        public bool $videoAutoPlay = true,
        public ?string $instanceId = null,
        public ?string $dataSource = null,
    ) {
        parent::__construct($id);

        $this->options = $options;

        $this->resolveModelOrClassName($modelOrClassName);

        $this->id = $this->id.'-mod';

        // merge into config
        $this->resolveConfig();
    }

    public function render(): View
    {
        return $this->renderView('media-modal', $this->getConfig('frontendTheme'));
    }
}
