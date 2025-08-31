<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

class MediaCarousel extends BaseComponent
{
    use ResolveModelOrClassName;

    public MediaCollection $mediaItems;
    public MediaCollection $media;// TODO duplocate with $mediaItems

    public int $mediaCount;
    public string $previewerId = '';

    public function __construct(
        public mixed $modelOrClassName,
        public ?string $mediaCollection = null,
        public ?array $mediaCollections = [],
        public bool $singleMedium = false,
        public bool $expandableInModal = true,
        public string $id = '',
        public ?string $frontendTheme = null,
        public bool $inModal = false,

    ) {
        parent::__construct($id, $frontendTheme);

        $this->resolveModelOrClassName($modelOrClassName);

        // Merge media from multiple or single collection
        $allMedia = collect(
            $this->mediaCollections ?: [$this->mediaCollection]
        )
            ->filter()// remove false values
            ->reduce(function (Collection $carry, string $collectionName) {
                if ($this->temporaryUpload) {
                    return $carry->merge(TemporaryUpload::forCurrentSession($collectionName));
                }
                return $carry->merge($this->model->getMedia($collectionName));
            }, collect());

        // Sort by 'priority' custom property (both TemporaryUpload and Media support getCustomProperty)
        $allMedia = $allMedia
            ->sortBy(fn($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
            ->values();

        $this->mediaItems = MediaCollection::make($allMedia);
        $this->media = $this->mediaItems;

        $this->mediaCount = $this->mediaItems->count();
        $this->frontendTheme = $frontendTheme ? $this->frontendTheme : config('media-library-extensions.frontend_theme', 'plain');
//        $this->id = $this->id.'-carousel';
        $this->id = $this->id.'-crs';
    }

    public function render(): View
    {
        return $this->getView('media-carousel', $this->frontendTheme);
    }
}
