<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\View\Components;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;
use Illuminate\View\View;

class Flash extends Component
{
    public string $flashPrefix;

    public function __construct(
    ) {
        $this->flashPrefix = config('media-library-extensions.flash_prefix');
    }

    public function render(): View
    {
        return view('media-library-extensions::components.debug');
    }
}
