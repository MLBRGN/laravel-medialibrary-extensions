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
        public string $sizeClass = 'modal-almost-fullscreen',
        public string $id = '',
    ) {
        parent::__construct($id);

        $this->mediaItems = $model->getMedia($mediaCollection);

    }

    public function render(): View
    {
        return view('media-library-extensions::components.media-manager-preview-modal');
    }
}
