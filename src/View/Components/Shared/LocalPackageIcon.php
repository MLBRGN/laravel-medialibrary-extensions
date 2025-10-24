<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Shared;

use Illuminate\View\Component;
use Illuminate\View\View;

class LocalPackageIcon extends Component
{
    public function render(): View
    {
        return view('media-library-extensions::components.shared.local-package-icon');
    }
}
