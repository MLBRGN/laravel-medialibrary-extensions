<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerDestroyForm extends BaseComponent
{
    public function __construct(

        public Media $medium,
        public string $id,
        public ?string $frontendTheme,

    ) {
        parent::__construct($id, $frontendTheme);
    }

    public function render(): View
    {
        return $this->getView('media-manager-destroy-form');
    }
}
