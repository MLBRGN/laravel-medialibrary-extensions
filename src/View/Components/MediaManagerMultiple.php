<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerMultiple extends BaseMediaManager
{
    public string $allowedMimeTypes = '';

    /** @var Collection<int, Media> */
    public Collection $media;

    public string $modalId;

    public function __construct(
        public ?Model $model = null,
        public ?string $mediaCollection = null,
        public bool $uploadEnabled = false,
        public ?string $uploadRoute = null,
        public string $uploadFieldName = 'media',
        public bool $destroyEnabled = false,
        public ?string $destroyRoute = null,
        public bool $showMediaUrl = false,
        public bool $setAsFirstEnabled = false,
        public bool $showOrder = false,
        public string $title = '',
        public string $id = '',
        public ?string $frontendTheme = null

    ) {
        parent::__construct($id, $frontendTheme);

        // set routes
        $this->uploadRoute = $this->uploadRoute ?? route(mle_prefix_route('media-upload-multiple'));

        // set allowed mimetypes
        $this->allowedMimeTypes = collect(config('media-library-extensions.allowed_mimes.image'))
            ->flatten()
            ->unique()
            ->implode(',');

        $this->media = $model->getMedia($mediaCollection);
        $this->modalId = 'media-manager-multiple-modal';
    }

    public function render(): View
    {
        return $this->getView('media-manager-multiple');
    }
}
