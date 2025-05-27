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
        public ?string $mediaCollectionName,
        public array $mediaCollections = [],
        public bool $singleMedium = false,
        public bool $clickToOpenInModal = true,// false to prevent endless inclusion
        public string $id = 'no-id',
    ) {
        parent::__construct($model, $mediaCollectionName, $id);

        foreach ($this->mediaCollections as $mediaCollection) {
            dump($mediaCollection);
        }
        // Combine the media items from the collections
        $this->mediaItems = $model->getMedia($mediaCollectionName);

        // Prepend the enterprise logo if it exists
        //        if (! is_null($logoCollectionName)) {
        //            $enterpriseLogo = $model->getMedia($logoCollectionName);
        //            if ($enterpriseLogo->isNotEmpty()) {
        //                $mediaItems = $enterpriseLogo->concat($mediaItems); // logo goes first
        //            }
        //        }

        // Append YouTube media if it exists
        //        if (! is_null($youtubeCollectionName)) {
        //            $mediaItems = $mediaItems->concat($model->getMedia($youtubeCollectionName));
        //        }
    }

    public function render(): View
    {
        return view('media-library-extensions::components.media-previewer');
    }
}
