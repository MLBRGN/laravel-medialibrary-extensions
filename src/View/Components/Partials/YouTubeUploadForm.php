<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Exception;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\HasMedia;

class YouTubeUploadForm extends BaseComponent
{

    public bool $mediaPresent = false;

    public string $mediaUploadRoute;// upload form action route
    public string $previewUpdateRoute;// route to update preview media when using ajax

    public HasMedia|null $model = null;
    public ?string $modelType = null;
    public mixed $modelId = null;
//    public bool $temporaryUpload = false;

    // TODO NOT RIGHT which collections to use?
    public function __construct(
        public ?string $youtubeCollection,
        public string $id,
        public ?string $frontendTheme,
        public ?string $mediaCollection,
        public ?string $documentCollection,
        public HasMedia|string|null $modelOrClassName = null,// either a modal that implements HasMedia or it's class name
        public bool $temporaryUpload = false,
        public string $temporaryUploadsUuid = '',
        public string $allowedMimeTypes = '',
        public bool $multiple = false,
        public bool $destroyEnabled = false,
        public bool $setAsFirstEnabled = false,
        public ?bool $useXhr = null,
    ) {
        parent::__construct($id, $frontendTheme);

        if (is_null($this->modelOrClassName)) {
            throw new Exception('model-or-class-name attribute must be set');
        }

        if ($this->modelOrClassName instanceof HasMedia) {
            $this->model = $this->modelOrClassName;
            $this->modelType = $this->modelOrClassName->getMorphClass();
            $this->modelId = $this->modelOrClassName->getKey();
        } elseif (is_string($this->modelOrClassName)) {
            $this->model = null;
            $this->modelType = $this->modelOrClassName;
            $this->modelId = null;
        } else {
            throw new Exception('model-or-class-name must be either a HasMedia model or a string representing the model class');
        }

        $this->mediaPresent = $this->model && $this->youtubeCollection
            ? $this->model->hasMedia($this->youtubeCollection)
            : false;

        $this->mediaUploadRoute = route(mle_prefix_route('media-upload-youtube'));
        $this->previewUpdateRoute = route(mle_prefix_route('preview-update'));// : route(mle_prefix_route('media-upload-single-preview'));
        $this->useXhr = !is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

    }

    public function render(): View
    {
        return $this->getPartialView('youtube-upload-form', $this->theme);
    }
}
