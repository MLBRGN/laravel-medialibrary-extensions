<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerPreview extends BaseComponent
{
    public string $allowedMimeTypes = '';

    public Collection $media;

    public HasMedia|null $model = null;

    public ?string $modelType = null;

    public mixed $modelId = null;

    public bool $temporaryUpload = false;

    public function __construct(
        public HasMedia|string $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public string $id = '',
        public ?string $imageCollection = '',
        public ?string $documentCollection = '',
        public ?string $youtubeCollection = '',
        public ?string $videoCollection = '',
        public ?string $audioCollection = '',
        public ?string $frontendTheme = null,
        public bool $destroyEnabled = false,
        public bool $setAsFirstEnabled = false,
        public bool $showOrder = false,
        public bool $showMenu = true,
        public bool $temporaryUploads = false,
        public ?bool $useXhr = true,
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

        // when non of the menu items visible, set showMenu to false
        if (!$destroyEnabled && !$showOrder && !$setAsFirstEnabled) {
            $this->showMenu = false;
        }

        $collectionNames = collect([
            $imageCollection,
            $youtubeCollection,
            $documentCollection,
            $videoCollection,
            $audioCollection,
        ])->filter(); // remove falsy values

        $this->media = $collectionNames
            ->reduce(function ($carry, $collectionName) use ($temporaryUploads) {
                if ($temporaryUploads) {
                    return $carry->merge(TemporaryUpload::forCurrentSession($collectionName));
                }

                if ($this->model) {
                    return $carry->merge($this->model->getMedia($collectionName));
                }

                return $carry;
            }, collect())
            ->sortBy(fn($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
            ->values();

    }

    public function render(): View
    {
        return $this->getView('media-manager-preview', $this->frontendTheme);
    }
}
