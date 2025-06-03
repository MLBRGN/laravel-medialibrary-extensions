<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Contracts\View\View;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

class MediaModal extends BaseMediaManager
{
    public MediaCollection $mediaItems;

    public function __construct(
        public ?HasMedia $model,
        public ?string $mediaCollection,
        public ?array $mediaCollections,
        public string $title,
        public string $sizeClass = 'modal-almost-fullscreen',
        public string $id = '',
        public ?string $frontendTheme = null,
        public bool $videoAutoPlay = true,
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
        $this->id = $this->id.'-modal';

    }

    public function render(): View
    {
        return $this->getView('media-modal');
    }
}
