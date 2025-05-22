<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\View\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

// !!!!! NOTE: remember to clean laravel cache after changes, otherwise cached views are used !!!!!
// clear the cache in the main application where the components are used by running php artisan optimize:clear
// and run composer dump-autoload

// -  TODO  when passed an attribute "autoplay" this attribute is passed on to media-preview-modal and
// will cause any youtube video to start autoplaying,
//    playing stops when closing the dialog or sliding to another slide

class MediaPreviewer extends BaseComponent
{
    public MediaCollection $mediaItems;

    public function __construct(
        public ?Model $model = null,
        public ?string $mediaCollectionName = null,
        public string $modalId = 'media-manager-multiple-modal',
        public string $title = '',
        public string $youtubeCollectionName = '',
        public string $logoCollectionName = '',
        public bool $singleMedium = false,
        public string $sizeClass = 'modal-almost-fullscreen',
        //        public bool $autoPlay = true,
    ) {
        parent::__construct($model, $mediaCollectionName);

        $this->mediaItems = $model->getMedia($mediaCollectionName);

    }

    public function render(): View
    {
        return view('media-library-extensions::components.media-manager-previewer');
    }
}
