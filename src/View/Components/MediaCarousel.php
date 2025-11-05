<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaCollections;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaCarousel extends BaseComponent
{
    use InteractsWithMediaCollections;
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    public Collection $media;

    public int $mediaCount;

    public string $previewerId = '';

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,
        public Media|TemporaryUpload|null $singleMedium = null, // when provided, skip collection lookups and use this medium
        public ?array $collections = [],
        public bool $expandableInModal = true,
        public array $options = [],
        public bool $inModal = false, // TODO used anywhere?
        public bool $previewMode = true // should the media-viewer be in preview mode (no autoplay, no document loading or not)
    ) {
        parent::__construct($id);

        $this->resolveModelOrClassName($modelOrClassName);

        $this->media = $this->resolveMediaFromCollections($this->collections);

        $this->mediaCount = $this->media->count();
        $this->id = $this->id.'-crs';

        // merge into config
        $this->initializeConfig();
    }

    public function render(): View
    {
        return $this->getView('media-carousel', $this->getConfig('frontendTheme'));
    }
}
