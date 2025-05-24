<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Flash extends Component
{
    public function __construct(
        public string $targetId
    ) {}

    public function render(): View
    {
        return view('media-library-extensions::components.flash');
    }
}
