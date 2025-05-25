<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\View\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

// !!!!! NOTE: remember to clean laravel cache after changes, otherwise cached views are used !!!!!
// clear the cache in the main application where the components are used by running php artisan optimize:clear
// and run composer dump-autoload

class MediaManagerMultiple extends BaseComponent
{
    public string $allowedMimeTypes = '';

    public function __construct(
        public ?Model $model = null,
        public ?string $mediaCollectionName = null,
        public bool $uploadEnabled = false,
        public ?string $uploadRoute = null,
        public string $uploadFieldName = 'media',
        public bool $destroyEnabled = false,
        public ?string $destroyRoute = null,
        public bool $showMediaUrl = false,
        public string $modalId = 'media-manager-multiple-modal',
        public bool $setAsFirstEnabled = false,
        public bool $showOrder = false,
        public string $title = '',
        public string $id = ''

    ) {
        parent::__construct($model, $mediaCollectionName, $id);

        // set routes
        $this->uploadRoute = $this->uploadRoute ?? route(mle_prefix_route('media-upload-multiple'));

        // can't set destroyRoute here, depends on medium to be destroyed

        // set allowed mimetypes
        $this->allowedMimeTypes = collect(config('media-library-extensions.allowed_mimes.image'))
            ->flatten()
            ->unique()
            ->implode(',');
    }

    public function render(): View
    {
        return view('media-library-extensions::components.media-manager-multiple');
    }
}
