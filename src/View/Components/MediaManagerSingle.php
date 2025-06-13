<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\View;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerSingle extends BaseComponent
{
    public ?Media $medium = null;

    public string $allowedMimeTypes = '';

    public function __construct(
        public ?HasMedia $model = null,
        public ?string $mediaCollection = null,
        public bool $uploadEnabled = false,
        public string $uploadFieldName = 'medium',
        public bool $destroyEnabled = false,
        public bool $showMediaUrl = false,
        public string $id = '',
        public ?string $frontendTheme = null

    ) {
        parent::__construct($id, $frontendTheme);

        // get medium only ever working with one medium
        $this->medium = $model->getFirstMedia($mediaCollection);

        // set allowed mimetypes
        $this->allowedMimeTypes = collect(config('media-library-extensions.allowed_mimes.image'))
            ->flatten()
            ->unique()
            ->implode(',');

        $this->frontend = $frontendTheme ?? config('media-library-extensions.frontend_theme', 'plain');
        $this->id = $this->id.'-media-manager-single';

    }

    public function render(): View
    {
        return $this->getView('media-manager-single',  $this->theme);
    }
}
