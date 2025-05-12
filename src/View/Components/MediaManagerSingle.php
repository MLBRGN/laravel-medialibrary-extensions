<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\View\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use InvalidArgumentException;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

class MediaManagerSingle extends BaseComponent
{
    public $theme;

    public $classes;

    public string $modelKebabName = '';

    public MediaCollection $media;

    // !IMPORTANT don't forget to clean laravel cache after changes, otherwise cached views are used !!!!!
    // clear the cache in the main application where the components are used by running php artisan optimize:clear
    public function __construct(
        public ?Model $model = null,
        public ?string $mediaCollectionName = null,
        public bool $uploadEnabled = false,
        public ?string $uploadRoute = null,
        public string $uploadFieldName = 'medium',
        public bool $destroyEnabled = false,
        public ?string $destroyRoute = null,
        public bool $showMediaUrl = false,
        public string $modalId = 'media-manager-single-modal',
        public string $title = '',
    ) {
        parent::__construct($model, $mediaCollectionName);

        // TODO NOT SURE IF I NEED THESE WHEN I HAVE A PACKAGE
        //        if (is_null($uploadRoute) && $uploadEnabled === true) {
        //            throw new InvalidArgumentException(
        //                __('media-library-extensions::messages.no-upload-route', ['component' => static::class])
        //            );
        //        }
        //
        //        if (is_null($destroyRoute) && $destroyEnabled === true) {
        //            throw new InvalidArgumentException(
        //                __('media-library-extensions::messages.no-destroy-route', ['component' => static::class])
        //            );
        //        }

    }

    public function render(): View|string
    {
        return view('media-library-extensions::components.media-manager-single');
    }
}
