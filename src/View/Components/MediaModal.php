<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Contracts\View\View;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

class MediaModal extends BaseComponent
{
    public MediaCollection $mediaItems;

    public function __construct(
        public ?HasMedia $model,
        public ?string $mediaCollection,
        public ?array $mediaCollections,
        public string $title,// TODO do i want this?
        public string $sizeClass = 'modal-almost-fullscreen',// TODO remove
        public string $id = '',
        public ?string $frontendTheme = null,
        public bool $videoAutoPlay = true,
    ) {
        parent::__construct($id, $frontendTheme);

        if ($model) {
            if (!empty($this->mediaCollections)) {
                // Use multiple collections if provided
                $allMedia = collect();
                foreach ($this->mediaCollections as $collectionName) {
                    if (!empty($collectionName)) {
                        $allMedia = $allMedia->merge($model->getMedia($collectionName));
                    }
                }
                $this->mediaItems = MediaCollection::make($allMedia);
            } elseif (!empty($this->mediaCollection)) {
                // Fallback to the single collection
                $this->mediaItems = $model->getMedia($this->mediaCollection);
            } else {
                // Fallback to a collection
                $this->mediaItems = MediaCollection::make();
            }
        } else {
            $this->mediaItems = MediaCollection::make();
        }
//        $this->frontend = $frontendTheme ?? config('media-library-extensions.frontend_theme', 'plain');
        $this->id = $this->id.'-modal';

    }

    public function render(): View
    {
        return $this->getView('media-modal',  $this->theme);
    }
}
