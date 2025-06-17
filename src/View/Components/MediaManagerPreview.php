<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Spatie\MediaLibrary\HasMedia;

class MediaManagerPreview extends BaseComponent
{
    public string $allowedMimeTypes = '';
    public bool $showMenu = false;

    public function __construct(
        public ?HasMedia $model = null,
        public Collection $media,
        public string $mediaCollection = '',
        public string $id = '',
        public ?string $frontendTheme = null,
        public bool $destroyEnabled = false,
        public bool $setAsFirstEnabled = false,
        public bool $showMediaUrl = false,
        public bool $showOrder = false,
    )
    {
        parent::__construct($id, $frontendTheme);

        if ($destroyEnabled || $showOrder || $setAsFirstEnabled) {
            $this->showMenu = true;
        } else {
            $this->showMenu = false;
        }
    }

    public function render(): View
    {
        return $this->getView('media-manager-preview',  $this->theme);
    }
}
