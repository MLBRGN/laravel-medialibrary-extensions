<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\View;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

class MediaCarousel extends BaseComponent
{
    public MediaCollection $mediaItems;

    public string $previewerId = '';

    public function __construct(
        public ?HasMedia $model,
        public ?string $mediaCollection = null,
        public ?array $mediaCollections = [],
        public bool $singleMedium = false,
        public bool $clickToOpenInModal = true,// false to prevent endless inclusion
        public string $id = '',
        public ?string $frontendTheme = null,
        public bool $inModal = false,

    ) {
        parent::__construct($id, $frontendTheme);

        if (! is_null($this->mediaCollection)) {
            $this->mediaItems = $model->getMedia($this->mediaCollection);
        } elseif (! empty($this->mediaCollections)) {
            $allMedia = collect();
            foreach ($this->mediaCollections as $collectionName) {
                $allMedia = $allMedia->merge($model->getMedia($collectionName));
            }
            $this->mediaItems = MediaCollection::make($allMedia);
        } else {
            $this->mediaItems = MediaCollection::make();
        }

        $this->frontend = $frontendTheme ?? config('media-library-extensions.frontend_theme', 'plain');

        $this->id = $this->id.'-carousel';
    }

    public function render(): View
    {
        return $this->getView('media-carousel', $this->theme);
    }
}
