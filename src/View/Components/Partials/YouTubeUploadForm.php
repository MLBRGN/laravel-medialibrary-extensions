<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\HasMedia;

class YouTubeUploadForm extends BaseComponent
{

    public bool $mediaPresent = false;

    public string $mediaUploadRoute;// upload form action route
    public string $previewRefreshRoute;// route to refresh preview media when using ajax

    public function __construct(

        public ?HasMedia $model,
        public ?string $youtubeCollection,
        public string $id,
        public ?string $frontendTheme,
        public ?string $mediaCollection,
        public ?string $documentCollection,
        public string $allowedMimeTypes = '',
        public bool $multiple = false,
        public bool $destroyEnabled = false,
        public bool $setAsFirstEnabled = false,
        public ?bool $useXhr = null,
    ) {
        parent::__construct($id, $frontendTheme);

        $this->mediaPresent = $this->model && $this->youtubeCollection
            ? $this->model->hasMedia($this->youtubeCollection)
            : false;

        $this->mediaUploadRoute = route(mle_prefix_route('media-upload-youtube'));
        $this->previewRefreshRoute = route(mle_prefix_route('media-upload-refresh-preview'));// : route(mle_prefix_route('media-upload-single-preview'));
        $this->useXhr = !is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

    }

    public function render(): View
    {
        return $this->getPartialView('youtube-upload-form', $this->theme);
    }
}
