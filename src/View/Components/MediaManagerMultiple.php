<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// !!!!! NOTE: remember to clean laravel cache after changes, otherwise cached views are used !!!!!
// clear the cache in the main application where the components are used by running php artisan optimize:clear
// and run composer dump-autoload

class MediaManagerMultiple extends BaseComponent
{
    public string $allowedMimeTypes = '';

    /** @var Collection<int, Media> */
    public Collection $media;

    public function __construct(
        public ?Model $model = null,
        public ?string $mediaCollection = null,
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
        parent::__construct($id);

        // set routes
        $this->uploadRoute = $this->uploadRoute ?? route(mle_prefix_route('media-upload-multiple'));

        // set allowed mimetypes
        $this->allowedMimeTypes = collect(config('media-library-extensions.allowed_mimes.image'))
            ->flatten()
            ->unique()
            ->implode(',');

        $this->media = $model->getMedia($mediaCollection);
    }

    public function render(): View
    {
        return view('media-library-extensions::components.media-manager-multiple');
    }
}
