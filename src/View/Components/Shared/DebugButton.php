<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Shared;

use Illuminate\View\Component;
use Illuminate\View\View;

class DebugButton extends Component
{
    public function render(): View
    {
        return view('media-library-extensions::components.shared.debug-button');
    }
}
