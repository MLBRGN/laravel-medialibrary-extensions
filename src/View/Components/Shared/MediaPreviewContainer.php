<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Shared;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class MediaPreviewContainer extends BaseComponent
{
    public function __construct(
        public string $id
    ) {
        parent::__construct($id);
    }

    public function render(): View
    {
        return $this->renderView('shared.media-preview-container');
    }
}
