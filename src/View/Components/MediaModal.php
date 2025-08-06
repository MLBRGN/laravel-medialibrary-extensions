<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\Contracts\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

class MediaModal extends BaseComponent
{
    public MediaCollection $mediaItems;

    public ?HasMedia $model = null;

    public ?string $modelType = null;

    public mixed $modelId = null;

    public bool $temporaryUpload = false;

    public function __construct(
        public HasMedia|string|null $modelOrClassName = null,
        public ?string $mediaCollection,
        public ?array $mediaCollections,
        public string $title,// TODO do i want this?
        public string $id = '',
        public ?string $frontendTheme = null,
        public bool $videoAutoPlay = true,
    ) {
        parent::__construct($id, $frontendTheme);

        if (is_null($modelOrClassName)) {
            throw new Exception('model-or-class-name attribute must be set');
        }

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
        $this->id = $this->id . '-modal';

    }

    public function render(): View
    {
        return $this->getView('media-modal', $this->theme);
    }
}
