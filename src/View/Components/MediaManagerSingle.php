<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerSingle extends BaseComponent
{
//    public ?Media $medium = null;
    public Collection $media;
    public string $mediaUploadRoute;// upload form action route
    public string $previewRefreshRoute;// route to refresh preview media when using ajax

    public string $allowedMimeTypes = '';

    public function __construct(
        public ?HasMedia $model = null,
        public ?string $mediaCollection = null,
        public bool $uploadEnabled = false,
        public string $uploadFieldName = 'medium',
        public bool $destroyEnabled = false,
        public bool $showMediaUrl = false,
        public string $id = '',
        public ?string $frontendTheme = null,
        public string $documentCollection = '',
        public string $youtubeCollection = '',
        public bool $setAsFirstEnabled = false,
        public ?bool $useXhr = true,
    ) {
        parent::__construct($id, $frontendTheme);

        // get medium only ever working with one medium
//        $this->medium = $model->getFirstMedia($mediaCollection);
        // NOTE: for simplicity and consistency with media manager multiple using getMedia for a collection although there should be only 1 medidum
        $this->media = $model->getMedia($mediaCollection);

        // set allowed mimetypes
        $this->allowedMimeTypes = collect(config('media-library-extensions.allowed_mimes.image'))
            ->flatten()
            ->unique()
            ->implode(',');

        $this->useXhr = !is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');
        $this->mediaUploadRoute = route(mle_prefix_route('media-upload-single'));
        $this->previewRefreshRoute = route(mle_prefix_route('media-upload-refresh-preview'));

        $this->id = $this->id.'-media-manager-single';

    }

    public function render(): View
    {
        return $this->getView('media-manager-single',  $this->theme);
    }
}
