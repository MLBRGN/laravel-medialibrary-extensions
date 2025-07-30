<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;

class MediaManagerPreview extends BaseComponent
{
    public string $allowedMimeTypes = '';
    public bool $showMenu = false;
    public Collection $media;

    public function __construct(
        public string $id = '',
        public ?HasMedia $model = null,
        public string $imageCollection = '',
        public string $documentCollection = '',
        public string $youtubeCollection = '',
        public ?string $frontendTheme = null,
        public bool $destroyEnabled = false,
        public bool $setAsFirstEnabled = false,
        public bool $showMediaUrl = false,
        public bool $showOrder = false,
        public bool $temporaryUploads = false,
    )
    {
        parent::__construct($id, $frontendTheme);

//        dd('session in preview class: '.session()->getId());
        if ($destroyEnabled || $showOrder || $setAsFirstEnabled) {
            $this->showMenu = true;
        } else {
            $this->showMenu = false;
        }

        $collections = collect();

        if ($temporaryUploads) {
             $collections = $collections->merge(TemporaryUpload::forCurrentSession());
        } else {
            if ($model) {
                if ($imageCollection) {
                    $collections = $collections->merge($model->getMedia($imageCollection));
                }

                if ($youtubeCollection) {
                    $collections = $collections->merge($model->getMedia($youtubeCollection));
                }

                if ($documentCollection) {
                    $collections = $collections->merge($model->getMedia($documentCollection));
                }
            }
        }
        $this->media = $collections;
    }

    public function render(): View
    {
        return $this->getView('media-manager-preview',  $this->theme);
    }
}
