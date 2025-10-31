<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaCarousel extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

//    public MediaCollection $mediaItems;
    public Collection $media;
//    public MediaCollection $media; // TODO duplicate with $mediaItems

    public int $mediaCount;

    public string $previewerId = '';

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,
        public Media|TemporaryUpload|null $singleMedium = null, // when provided, skip collection lookups and use this medium
        public ?string $mediaCollection = null,// TODO why do i have 2
        public ?array $mediaCollections = [],
        public ?array $collections = [],
        public bool $expandableInModal = true,
        public array $options = [],
        public bool $inModal = false,
    ) {
        parent::__construct($id);

        $this->resolveModelOrClassName($modelOrClassName);


        $this->media = collect();

        // CASE 1: If a single medium is provided, use only that.
        if ($this->singleMedium instanceof Media || $this->singleMedium instanceof TemporaryUpload) {
            $this->media->push($this->singleMedium);
        } else {
            $this->media = collect($collections)
                ->filter(fn($collectionName
                ) => !is_null($collectionName) && $collectionName !== '') // remove null or empty
                ->flatMap(function (?string $collectionName, string $collectionType) {
                    if ($this->temporaryUploadMode) {
                        if (!empty($collectionName)) {
                            return TemporaryUpload::forCurrentSession($collectionName);
                        }
                    }

                    if ($this->model) {
                        return $this->model->getMedia($collectionName);
                    }

                    return [];
                })
                ->sortBy(fn($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
                ->values();
        }
        // Merge media from multiple or single collection
//        $allMedia = collect(
//            $this->mediaCollections ?: [$this->mediaCollection]
//        )
//            ->filter()// remove false values
//            ->reduce(function (Collection $carry, string $collectionName) {
//                if ($this->temporaryUploadMode) {
//                    return $carry->merge(TemporaryUpload::forCurrentSession($collectionName));
//                }
//
//                return $carry->merge($this->model->getMedia($collectionName));
//            }, collect());
//
//        // Sort by 'priority' custom property (both TemporaryUpload and Media support getCustomProperty)
//        $allMedia = $allMedia
//            ->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
//            ->values();
//
//        $this->mediaItems = MediaCollection::make($allMedia);
//        $this->media = $this->mediaItems;

        // TODO use Collection or MediaCollection?
        // $this->mediaItems = MediaCollection::make($allMedia);
        // $this->media = $this->mediaItems;
        // $this->mediaCount = $this->mediaItems->count();
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
