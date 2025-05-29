<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerSingle extends BaseMediaManager
{
    public ?Media $medium = null;

    public string $allowedMimeTypes = '';

    public string $modalId;

    public function __construct(
        public ?Model $model = null,
        public ?string $mediaCollection = null,
        public bool $uploadEnabled = false,
        public string $uploadFieldName = 'medium',
        public bool $destroyEnabled = false,
        public bool $showMediaUrl = false,
        public string $title = '',
        public string $id = '',
        public ?string $frontendTheme = null

    ) {
        parent::__construct($id, $frontendTheme);

        // get medium only ever working with one medium
        $medium = $this->medium = $model->getFirstMedia($mediaCollection);

        // an empty action attribute may cause the parent form to submit, check for empty route
        //        if ($medium) {
        //            $this->destroyRoute = ! empty($this->destroyRoute) ? $this->destroyRoute : route(mle_prefix_route('medium-destroy'), $medium->id);
        //        }

        // set allowed mimetypes
        $this->allowedMimeTypes = collect(config('media-library-extensions.allowed_mimes.image'))
            ->flatten()
            ->unique()
            ->implode(',');

        $this->frontend = $frontendTheme ?? config('media-library-extensions.frontend_theme', 'plain');
        $this->modalId = 'media-manager-single-modal';

    }

    public function render(): View
    {
        return $this->getView('media-manager-single');
    }
}
