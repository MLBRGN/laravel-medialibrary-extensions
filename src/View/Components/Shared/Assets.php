<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Shared;

use Illuminate\View\Component;
use Illuminate\View\View;

class Assets extends Component
{
    public function __construct(
        public ?string $frontendTheme = null,
        public bool $includeCss = false,
        public bool $includeJs = false,
        public bool $includeCarouselJs = false,
        public bool $includeImageEditorJs = false,
        public bool $includeImageEditorModalJs = false,
        public bool $includeMediaModalJs = false,
        public bool $includeFormSubmitter = false,
        public bool $includeLiteYoutube = false,
        public bool $includeTinymceCustomFilePickerIframeJs = false
    ) {
        $this->frontendTheme = $frontendTheme ? $this->frontendTheme : config('media-library-extensions.frontend_theme', 'plain');
    }

    public function render(): View
    {
        return view('media-library-extensions::components.shared.assets');
    }
}
