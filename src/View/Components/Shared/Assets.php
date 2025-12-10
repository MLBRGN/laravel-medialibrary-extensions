<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Shared;

use Illuminate\View\Component;
use Illuminate\View\View;

class Assets extends Component
{
    public array $config;

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
        public bool $includeTinymceCustomFilePickerIframeJs = false
    ) {
        // Default theme from config
        $this->frontendTheme ??= config('media-library-extensions.frontend_theme', 'plain');

        // Build the configuration array passed to the loader.js file
        $this->config = [
            'theme' => $this->frontendTheme,
            'includeCss' => $this->includeCss,
            'includeJs' => $this->includeJs,
            'includeCarouselJs' => $this->includeCarouselJs,
            'includeTinymceIframeJs' => $this->includeTinymceCustomFilePickerIframeJs,
            'includeImageEditorModalJs' => $this->includeImageEditorModalJs,
            'includeMediaModalJs' => $this->includeMediaModalJs,
            'includeImageEditorJs' => $this->includeImageEditorJs,
            'includeMediaManagerSubmitter' => $this->includeMediaManagerSubmitter,
            'includeMediaManagerLabSubmitter' => $this->includeMediaManagerLabSubmitter,
            'includeLiteYoutube' => $this->includeLiteYoutube,

            // Translation strings (CSP-safe: no inline script)
            'translations' => [
                'csrf_token_mismatch' =>
                    __('media-library-extensions::http.csrf_token_mismatch'),

                'unauthenticated' =>
                    __('media-library-extensions::http.unauthenticated'),

                'forbidden' =>
                    __('media-library-extensions::http.forbidden'),

                'not_found' =>
                    __('media-library-extensions::http.not_found'),

                'validation_failed' =>
                    __('media-library-extensions::http.validation_failed'),

                'too_many_requests' =>
                    __('media-library-extensions::http.too_many_requests'),

                'server_error' =>
                    __('media-library-extensions::http.server_error'),

                'unknown_error' =>
                    __('media-library-extensions::http.unknown_error'),

                'medium_replaced' =>
                    __('media-library-extensions::messages.medium_replaced'),

                'medium_replacement_failed' =>
                    __('media-library-extensions::messages.medium_replacement_failed'),
            ],
        ];
    }

    public function render(): View
    {
        return view('media-library-extensions::components.shared.assets');
    }
}
