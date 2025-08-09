<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
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
        public bool $clickToOpenInModal = true,// false to prevent endless inclusion
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

        $allMedia = collect();

        if ($this->temporaryUpload) {
            if (!empty($this->mediaCollections)) {
                foreach ($this->mediaCollections as $collectionName) {
                    if (!empty($collectionName)) {
                        $allMedia = $allMedia->merge(TemporaryUpload::forCurrentSession($collectionName));
                    }
                }
            } elseif (!empty($this->mediaCollection)) {
                $allMedia = TemporaryUpload::forCurrentSession($this->mediaCollection);
            }
        } elseif ($this->model) {
            if (!empty($this->mediaCollections)) {
                foreach ($this->mediaCollections as $collectionName) {
                    if (!empty($collectionName)) {
                        $allMedia = $allMedia->merge($this->model->getMedia($collectionName));
                    }
                }
            } elseif (!empty($this->mediaCollection)) {
                $allMedia = $this->model->getMedia($this->mediaCollection);
            }
        }

        $this->mediaItems = MediaCollection::make($allMedia);
        $this->media = $this->mediaItems;

        $this->mediaCount = $this->mediaItems->count();

        $this->frontendTheme = $frontendTheme ?? config('media-library-extensions.frontend_theme', 'plain');

        $this->id = $this->id.'-carousel';
    }

    public function render(): View
    {
        return $this->getView('media-carousel', $this->frontendTheme);
    }
}
