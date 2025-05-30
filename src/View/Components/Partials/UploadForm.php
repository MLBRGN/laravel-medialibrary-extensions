<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\HasMedia;

class UploadForm extends BaseComponent
{
    public function __construct(

        public ?HasMedia $model,
        public ?string $mediaCollection,
        public string $id,
        public ?string $frontendTheme,
        public string $allowedMimeTypes,
        public bool $multiple = false,
        public bool $mediaPresent = false,
    ) {
        parent::__construct($id, $frontendTheme);
    }

    public function render(): View
    {
        return $this->getPartialView('upload-form');
    }
}
