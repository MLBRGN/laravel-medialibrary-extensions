<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Preview;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class MediaEmptyState extends Component
{
    public function render(): View
    {
        return view('media-library-extensions::components.bootstrap-5.preview.media-empty-state');
    }
}
