<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

// !!!!! NOTE: remember to clean laravel cache after changes, otherwise cached views are used !!!!!
// clear the cache in the main application where the components are used by running php artisan optimize:clear
// and run composer dump-autoload

// Documentation:
//    -   Used by both manager-multiple.blade and previewer.blade
//    -   Only works with one YT video at the moment, javascript assumes
//    the video has an id of "yt-video-slid"
//    -   when passed an attribute "autoplay" this attribute is passed on to media-preview-modal and
//    will cause any youtube video to start autoplaying,
//    playing stops when closing the dialog or sliding to another slide

class MediaPreviewer extends BaseComponent
{
    public MediaCollection $mediaItems;

    public function __construct(
        public ?Model $model,
        public ?string $mediaCollection = null,
        public array $mediaCollections = [],
        public bool $singleMedium = false,
        public bool $clickToOpenInModal = true,// false to prevent endless inclusion
        public string $id = 'no-id',
    )
    {
        parent::__construct($model, $mediaCollection, $id);

        if (!is_null($this->mediaCollection)) {
            $this->mediaItems = $model->getMedia($this->mediaCollection);
        } elseif (!empty($this->mediaCollections)) {
            $allMedia = collect();
            foreach ($this->mediaCollections as $collectionName) {
                $allMedia = $allMedia->merge($model->getMedia($collectionName));
            }
            $this->mediaItems = MediaCollection::make($allMedia);
        } else {
            $this->mediaItems = MediaCollection::make();
        }

    }

    public function render(): View
    {
        return view('media-library-extensions::components.media-previewer');
    }
}
