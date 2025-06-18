<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Spinner extends Component
{
    public function __construct(
    ) {}
    public function render(): View
    {
        return view('media-library-extensions::components.spinner');
    }

}
