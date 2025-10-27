<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Shared;

use Illuminate\View\Component;
use Illuminate\View\View;

class MediaPreviewContainer extends Component
{
    public function __construct(
        public string $id
    ) {}

    public function render(): View
    {
        return view('media-library-extensions::components.shared.media-preview-container');
    }
}
