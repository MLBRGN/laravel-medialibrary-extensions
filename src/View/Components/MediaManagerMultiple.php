<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerMultiple extends BaseComponent
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
    )
    {
        parent::__construct($id, $frontendTheme);

        // Get the base image MIME types
        $mimeTypes = collect(config('media-library-extensions.allowed_mimetypes.image'));

        // Conditionally merge document MIME types
        if ($this->documentCollection) {
            $documentMimeTypes = config('media-library-extensions.allowed_mimetypes.document');
            $mimeTypes = $mimeTypes->merge($documentMimeTypes);
        }

        // Flatten, unique, and implode the final list
        $this->allowedMimeTypes = $mimeTypes
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
        $this->mediaUploadRoute = route(mle_prefix_route('media-upload-multiple'));
        $this->previewRefreshRoute = route(mle_prefix_route('media-upload-refresh-preview'));
        $this->youtubeUploadRoute = route(mle_prefix_route('media-upload-youtube'));
//        $this->destroyRoute = route(mle_prefix_route('medium-destroy'));
//        $this->setAsFirstRoute = route(mle_prefix_route('set-as-first'));

        $this->id = $this->id.'-media-manager-multiple';
    }

    public function render(): View
    {
        return $this->getView('media-manager-multiple',  $this->theme);
    }
}
