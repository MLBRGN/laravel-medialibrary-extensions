<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManager extends BaseComponent
{
    public string $allowedMimeTypes = '';

    /** @var Collection<int, Media> */
    public Collection $media;
    public string $mediaUploadRoute;// upload form action route
    public string $previewRefreshRoute;// route to refresh preview media when using ajax
    public string $youtubeUploadRoute;// route to upload youtube video using ajax

    public function __construct(
        public ?HasMedia $model = null,
        public string $mediaCollection = '',
        public string $documentCollection = '',
        public string $youtubeCollection = '',
        public bool $uploadEnabled = false,
        public string $uploadFieldName = 'media',
        public bool $destroyEnabled = false,
        public bool $setAsFirstEnabled = false,
        public bool $showMediaUrl = false,
        public bool $showOrder = false,
        public string $id = '',
        public ?string $frontendTheme = null,
        public ?bool $useXhr = true,
        public bool $multiple = false,
    )
    {
        parent::__construct($id, $frontendTheme);

        // set allowed mimetypes
        $this->allowedMimeTypes = collect(config('media-library-extensions.allowed_mimes.image'))
            ->flatten()
            ->unique()
            ->implode(',');

        $collections = collect();
        if ($model) {
            if ($mediaCollection) {
                $collections = $collections->merge($model->getMedia($mediaCollection));
            }

            if ($youtubeCollection) {
                $collections = $collections->merge($model->getMedia($youtubeCollection));
            }

            if ($documentCollection) {
                $collections = $collections->merge($model->getMedia($documentCollection));
            }
        }
        $this->media = $collections;

        $this->useXhr = !is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

        // routes, set-as-first and destroy are medium specific routes, so not defined here
        $this->previewRefreshRoute = route(mle_prefix_route('media-upload-refresh-preview'));
        $this->youtubeUploadRoute = route(mle_prefix_route('media-upload-youtube'));

        if($this->multiple) {
            $this->uploadFieldName = config('media-library-extensions.upload_field_name_multiple');
            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-multiple'));
            $this->id = $this->id.'-media-manager-multiple';
        } else {
            $this->uploadFieldName = config('media-library-extensions.upload_field_name_single');
            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-single'));
            $this->id = $this->id.'-media-manager-single';
        }
    }

    public function render(): View
    {
        return $this->getView('media-manager', $this->theme);
    }
}
