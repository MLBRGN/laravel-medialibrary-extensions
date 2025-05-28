<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerSingle extends BaseMediaManager
{
    public ?Media $medium = null;

    /** @var Collection<int, Media> */
    //    public Collection $media;

    public string $allowedMimeTypes = '';

    public function __construct(
        public ?Model $model = null,
        public ?string $mediaCollection = null,
        public bool $uploadEnabled = false,
        public ?string $uploadRoute = null,
        public string $uploadFieldName = 'medium',
        public bool $destroyEnabled = false,
        public ?string $destroyRoute = null,
        public bool $showMediaUrl = false,
        public string $modalId = 'media-manager-single-modal',
        public string $title = '',
        public string $id = '',
        public ?string $frontendTheme = null

    ) {
        parent::__construct($id, $frontendTheme);

        // get medium only ever working with one medium
        $medium = $this->medium = $model->getFirstMedia($mediaCollection);

        // set routes
        $this->uploadRoute = $this->uploadRoute ?? route(mle_prefix_route('media-upload-single'));

        // an empty action attribute may cause the parent form to submit, check for empty route
        if ($medium) {
            $this->destroyRoute = ! empty($this->destroyRoute) ? $this->destroyRoute : route(mle_prefix_route('medium-destroy'), $medium->id);
        }

        // set allowed mimetypes
        $this->allowedMimeTypes = collect(config('media-library-extensions.allowed_mimes.image'))
            ->flatten()
            ->unique()
            ->implode(',');

        $this->frontend = $frontendTheme ?? config('media-library-extensions.frontend_theme', 'plain');

    }

    public function render(): View
    {
        return $this->getView('media-manager-single');
    }
}
