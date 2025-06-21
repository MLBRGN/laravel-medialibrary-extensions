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

    public function __construct(

        public ?HasMedia $model,
        public ?string $uploadToCollection,
        public ?string $imageCollection,
        public ?string $documentCollection,
        public ?string $youtubeCollection,
        public string $id,
        public ?string $frontendTheme,
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
        $allowedMimeTypesFromConfig = collect(config('media-library-extensions.allowed_mimetypes', []))->flatten();
        $mimeTypeLabels = config('media-library-extensions.mimeTypeLabels');
        $this->allowedMimeTypesHuman = $allowedMimeTypesFromConfig
            ->map(fn ($mime) => $mimeTypeLabels[$mime] ?? $mime)
            ->join(', ');
        $this->allowedMimeTypes = ! empty($this->allowedMimeTypes) ? $this->allowedMimeTypes : $allowedMimeTypesFromConfig->join(', ');

        $this->mediaPresent = $this->model && $this->uploadToCollection
            ? $this->model->hasMedia($this->uploadToCollection)
            : false;

        $this->useXhr = !is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

        return $this->getPartialView('upload-form', $this->theme);
    }
}
