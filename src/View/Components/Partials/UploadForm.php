<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\HasMedia;

class UploadForm extends BaseComponent
{
    public bool $mediaPresent = false;
    public string $allowedMimeTypesHuman = '';

    public string $formActionRoute;// upload form action route
    public string $previewRefreshRoute;// route to refresh preview media when using ajax

    public function __construct(

        public ?HasMedia $model,
        public ?string $mediaCollection,
        public ?string $documentCollection,
        public ?string $youtubeCollection,
        public string $id,
        public ?string $frontendTheme,
        public string $allowedMimeTypes = '',
        public bool $multiple = false,
        public ?bool $useXHR = null,
        public bool $destroyEnabled = false,
        public bool $setAsFirstEnabled = false,
    ) {
        parent::__construct($id, $frontendTheme);
    }

    public function render(): View
    {
//        dd($this->allowedMimeTypes);
        // TODO not right
        $allowedImageMimeTypesFromConfig = config('media-library-extensions.allowed_mimetypes.image', []);
        $mimeTypeLabels = config('media-library-extensions.mimeTypeLabels');
        $this->allowedMimeTypesHuman = collect($allowedImageMimeTypesFromConfig)
            ->map(fn ($mime) => $mimeTypeLabels[$mime] ?? $mime)
            ->join(', ');
        $this->allowedMimeTypes = ! empty($this->allowedMimeTypes) ? $this->allowedMimeTypes : collect(config('media-library-extensions.allowed_mimetypes.image'))->flatten()->join(', ');


        $this->mediaPresent = $this->model && $this->mediaCollection
            ? $this->model->hasMedia($this->mediaCollection)
            : false;

        $this->formActionRoute = $this->multiple ? route(mle_prefix_route('media-upload-multiple')) : route(mle_prefix_route('media-upload-single'));
        $this->previewRefreshRoute = route(mle_prefix_route('media-upload-refresh-preview'));// : route(mle_prefix_route('media-upload-single-preview'));

        $this->useXHR = !is_null($this->useXHR) ? $this->useXHR : config('media-library-extensions.use_xhr');
        return $this->getPartialView('upload-form', $this->theme);
    }
}
