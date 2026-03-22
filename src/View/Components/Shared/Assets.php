<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Shared;

use Illuminate\View\Component;
use Illuminate\View\View;

class Assets extends Component
{
    public array $assetConfig;

    public function __construct(
        public ?string $frontendTheme = null,
        public bool $includeCss = false,
        public bool $includeJs = false,
        public bool $includeCarouselJs = false,
        public bool $includeImageEditorJs = false,
        public bool $includeImageEditorModalJs = false,
        public bool $includeMediaModalJs = false,
        public bool $includeMediaManagerSubmitter = false,
        public bool $includeMediaManagerLabSubmitter = false,
        public bool $includeLiteYoutube = false,
        public bool $includeTinymceCustomFilePickerIframeJs = false,
        public string $for = 'unknown',
    ) {
        // Default theme from config
        $this->frontendTheme ??= config('media-library-extensions.frontend_theme', 'plain');

        // Build the configuration array passed to the loader.js file
        $this->assetConfig = [
            'assets' => [
                'css' => $this->includeCss,
                'js' => $this->includeJs,
                'carousel' => $this->includeCarouselJs,
                'tinymceIframe' => $this->includeTinymceCustomFilePickerIframeJs,
                'imageEditorModal' => $this->includeImageEditorModalJs,
                'mediaModal' => $this->includeMediaModalJs,
                'imageEditor' => $this->includeImageEditorJs,
                'mediaManagerSubmitter' => $this->includeMediaManagerSubmitter,
                'mediaManagerLabSubmitter' => $this->includeMediaManagerLabSubmitter,
                'liteYoutube' => $this->includeLiteYoutube,
            ],
            'for' => $this->for, // keep track of which config belongs to what
            'theme' => $this->frontendTheme,

            // Translation strings (CSP-safe: no inline script)
            'translations' => [
                'csrf_token_mismatch' => __('media-library-extensions::http.csrf_token_mismatch'),

                'unauthenticated' => __('media-library-extensions::http.unauthenticated'),

                'forbidden' => __('media-library-extensions::http.forbidden'),

                'not_found' => __('media-library-extensions::http.not_found'),

                'validation_failed' => __('media-library-extensions::http.validation_failed'),

                'too_many_requests' => __('media-library-extensions::http.too_many_requests'),

                'server_error' => __('media-library-extensions::http.server_error'),

                'unknown_error' => __('media-library-extensions::http.unknown_error'),

                'medium_replaced' => __('media-library-extensions::messages.medium_replaced'),

                'medium_replacement_failed' => __('media-library-extensions::messages.medium_replacement_failed'),
            ],
        ];
    }

    public function render(): View
    {
        return view('media-library-extensions::components.shared.assets');
    }
}
