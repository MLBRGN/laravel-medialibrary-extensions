<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public string $modalId,
        public string $title,
        public bool $noPadding = false,
        public bool $showHeader = true,
        public bool $showBody = true,
        public string $sizeClass = 'modal-lg',
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('media-library-extensions::components.modal');
    }
}
