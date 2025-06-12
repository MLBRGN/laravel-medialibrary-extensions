<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\View;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerPreview extends BaseComponent
{
    public string $allowedMimeTypes = '';
    public bool $showMenu = false;

    public function __construct(
        public ?HasMedia $model = null,
        public string $mediaCollection = '',
        public Media $medium,
        public string $id = '',
        public ?string $frontendTheme = null,
        public ?int $loopIndex = 0,
        public bool $destroyEnabled = false,
        public bool $setAsFirstEnabled = false,
        public bool $showMediaUrl = false,
        public bool $showOrder = false,
        public bool $isFirstInCollection = false,
    )
    {
        parent::__construct($id, $frontendTheme);

        if ($destroyEnabled || $showOrder || $setAsFirstEnabled) {
            $this->showMenu = true;
        }
    }

    public function render(): View
    {
        return $this->getView('media-manager-preview',  $this->theme);
    }
}
