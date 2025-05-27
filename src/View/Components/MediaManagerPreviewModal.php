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

class MediaManagerPreviewModal extends BaseComponent
{
    public MediaCollection $mediaItems;

    public function __construct(
        public ?Model $model,
        public ?string $mediaCollection,
        public string $modalId,
        public string $title,
        public string $youtubeCollectionName = '',
        public string $logoCollectionName = '',
        public bool $singleMedium = false,
        public string $sizeClass = 'modal-almost-fullscreen',
        public string $id = '',
    ) {
        parent::__construct($model, $mediaCollection, $id);

        // Combine the media items from the collections
        $this->mediaItems = $model->getMedia($mediaCollection);

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
        return view('media-library-extensions::components.media-manager-preview-modal');
    }
}
