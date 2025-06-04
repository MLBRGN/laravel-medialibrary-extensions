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
        public ?string $mediaCollection,
        public string $id,
        public ?string $frontendTheme,
        public string $allowedMimeTypes = '',
        public bool $multiple = false,
    ) {
        parent::__construct($id, $frontendTheme);
    }

    public function render(): View
    {
        $allowedImageMimeTypesFromConfig = config('media-library-extensions.allowed_mimetypes.image', []);
        $mimeTypeLabels = config('media-library-extensions.mimeTypeLabels');
        $this->allowedMimeTypesHuman = collect($allowedImageMimeTypesFromConfig)
            ->map(fn ($mime) => $mimeTypeLabels[$mime] ?? $mime)
            ->join(', ');
        $this->allowedMimeTypes = ! empty($this->allowedMimeTypes) ? $this->allowedMimeTypes : collect(config('media-library-extensions.allowed_mimetypes.image'))->flatten()->join(', ');


        $this->mediaPresent = $this->model && $this->mediaCollection
            ? $this->model->hasMedia($this->mediaCollection)
            : false;

        return $this->getPartialView('upload-form');
    }
}
