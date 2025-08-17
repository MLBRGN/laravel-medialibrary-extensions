<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

class MediaCarousel extends BaseComponent
{
    public MediaCollection $mediaItems;
    public MediaCollection $media;// TODO duplocate with $mediaItems

    public int $mediaCount;

    public string $previewerId = '';

    public HasMedia|null $model = null;

    public ?string $modelType = null;

    public mixed $modelId = null;

    public bool $temporaryUpload = false;

    public function __construct(
        public HasMedia|string $modelOrClassName,
        public ?string $mediaCollection = null,
        public ?array $mediaCollections = [],
        public bool $singleMedium = false,
        public bool $expandableInModal = true,
        public string $id = '',
        public ?string $frontendTheme = null,
        public bool $inModal = false,

    ) {
        parent::__construct($id, $frontendTheme);

        if ($modelOrClassName instanceof HasMedia) {
            $this->model = $modelOrClassName;
            $this->modelType = $modelOrClassName->getMorphClass();
            $this->modelId = $modelOrClassName->getKey();
        } elseif (is_string($modelOrClassName)) {
            $this->model = null;
            $this->modelType = $modelOrClassName;
            $this->modelId = null;
            $this->temporaryUpload = true;
        } else {
            throw new Exception('model-or-class-name must be either a HasMedia model or a string representing the model class');
        }

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
        $this->frontendTheme = $frontendTheme ?? config('media-library-extensions.frontend_theme', 'plain');
//        $this->id = $this->id.'-carousel';
        $this->id = $this->id.'-crs';
    }

    public function render(): View
    {
        return $this->getView('media-carousel', $this->frontendTheme);
    }
}
