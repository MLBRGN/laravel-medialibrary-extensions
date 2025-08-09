<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;

class MediaManagerPreview extends BaseComponent
{
    public string $allowedMimeTypes = '';

    public bool $showMenu = false;

    public Collection $media;

    public HasMedia|null $model = null;

    public ?string $modelType = null;

    public mixed $modelId = null;

    public bool $temporaryUpload = false;

    public function __construct(
        public HasMedia|string $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public string $id = '',
        public string $imageCollection = '',
        public string $documentCollection = '',
        public string $youtubeCollection = '',
        public ?string $frontendTheme = null,
        public bool $destroyEnabled = false,
        public bool $setAsFirstEnabled = false,
        public bool $showMediaUrl = false,
        public bool $showOrder = false,
        public bool $temporaryUploads = false,
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

        if ($destroyEnabled || $showOrder || $setAsFirstEnabled) {
            $this->showMenu = true;
        } else {
            $this->showMenu = false;
        }

        $collections = collect();

        if ($temporaryUploads) {
            if ($imageCollection) {
                $collections = $collections->merge(TemporaryUpload::forCurrentSession($imageCollection));
            }

            if ($youtubeCollection) {
                $collections = $collections->merge(TemporaryUpload::forCurrentSession($youtubeCollection));
            }

            if ($documentCollection) {
                $collections = $collections->merge(TemporaryUpload::forCurrentSession($documentCollection));
            }
        } elseif ($this->model) {
                if ($imageCollection) {
                    $collections = $collections->merge($this->model->getMedia($imageCollection));
                }

                if ($youtubeCollection) {
                    $collections = $collections->merge($this->model->getMedia($youtubeCollection));
                }

                if ($documentCollection) {
                    $collections = $collections->merge($this->model->getMedia($documentCollection));
                }
        }
        $this->media = $collections;
    }

    public function render(): View
    {
        return $this->getView('media-manager-preview', $this->frontendTheme);
    }
}
