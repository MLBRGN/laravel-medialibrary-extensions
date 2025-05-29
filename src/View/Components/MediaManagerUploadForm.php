<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

class MediaManagerUploadForm extends BaseComponent
{
    public function __construct(

        public ?Model $model,
        public ?string $mediaCollection,
        public string $id,
        public ?string $frontendTheme,
        public string $uploadRoute,
        public string $allowedMimeTypes,
        public string $uploadFieldName,
        public bool $multiple = false,
        public bool $mediaPresent = false,
    ) {
        parent::__construct($id, $frontendTheme);
    }

    public function render(): View
    {
        return $this->getView('media-manager-upload-form');
    }
}
