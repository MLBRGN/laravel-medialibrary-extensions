<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

class MediaPreviewer extends BaseMediaPreviewer
{
    public MediaCollection $mediaItems;

    public function __construct(
        public ?Model $model,
        public ?string $mediaCollection = null,
        public ?array $mediaCollections = [],
        public bool $singleMedium = false,
        public bool $clickToOpenInModal = true,// false to prevent endless inclusion
        public string $id = 'no-id',
        public ?string $frontendTheme = null

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

    }

    public function render(): View
    {
        return $this->getView('media-previewer');
    }
}
