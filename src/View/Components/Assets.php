<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\View;

class Assets extends BaseComponent
{

    public function __construct(
        public ?string $frontendTheme = null,
        public bool $includeCss = false,
        public bool $includeJs = false,
        public bool $includeYoutubeIframeApi = false,
    ) {
        parent::__construct('mle-assets', $frontendTheme);
        $this->frontend = $frontendTheme ?? config('media-library-extensions.frontend_theme', 'plain');
    }

    public function render(): View
    {
        return $this->getPartialView('assets');
    }
}
