<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\Component;
use Illuminate\View\View;

class Assets extends Component
{

    public string $frontend;
    public function __construct(
        public ?string $frontendTheme = null,
        public bool $includeCss = false,
        public bool $includeJs = false,
        public bool $includeYoutubePlayer = false,
    ) {
        $this->frontend = $frontendTheme ?? config('media-library-extensions.frontend_theme', 'plain');
    }

    public function render(): View
    {
        return view('media-library-extensions::components.partial.assets');
    }
}
