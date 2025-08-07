<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Exception;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\HasMedia;

class UploadForm extends BaseComponent
{
    public bool $mediaPresent = false;

    public string $allowedMimeTypesHuman = '';

    public ?HasMedia $model = null;

    public ?string $modelType = null;

    public mixed $modelId = null;
    //    public bool $temporaryUpload = false;

    public function __construct(
        public string $id,
        public ?string $frontendTheme,
        public ?string $imageCollection,
        public ?string $documentCollection,
        public ?string $youtubeCollection,
        public HasMedia|string|null $modelOrClassName = null,// either a modal that implements HasMedia or it's class name
        public bool $temporaryUpload = false,
        public string $allowedMimeTypes = '',
        public bool $multiple = false,
        public bool $destroyEnabled = false,
        public bool $setAsFirstEnabled = false,
        public ?bool $useXhr = null,
    ) {
        parent::__construct($id, $frontendTheme);
    }

    public function render(): View
    {
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

        $allowedMimeTypesFromConfig = collect(config('media-library-extensions.allowed_mimetypes', []))->flatten();
        $mimetype_labels = config('media-library-extensions.mimetype_labels');
        $this->allowedMimeTypesHuman = $allowedMimeTypesFromConfig
            ->map(fn ($mime) => $mimetype_labels[$mime] ?? $mime)
            ->join(', ');
        $this->allowedMimeTypes = ! empty($this->allowedMimeTypes) ? $this->allowedMimeTypes : $allowedMimeTypesFromConfig->join(', ');

        // TODO look at this
        $this->mediaPresent = $this->model && $this->imageCollection
            ? $this->model->hasMedia($this->imageCollection)
            : false;

        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

        return $this->getPartialView('upload-form', $this->frontendTheme);
    }
}
