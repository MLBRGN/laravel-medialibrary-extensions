<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageResponsive extends Component
{
    public function __construct(
        public ?Media $medium = null,
    ) {}

    public function render(): View
    {
        return view('media-library-extensions::components.image-responsive');
    }
}
