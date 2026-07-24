<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Shared;

use Illuminate\View\Component;
use Illuminate\View\View;

class Assets extends Component
{
    public array $assetConfig;

    public function __construct(
        public ?string $theme = null,
        public bool $includeCss = false,
        public bool $includeJs = false,
        public bool $includeCarouselJs = false,
        public bool $includeImageEditorJs = false,
        public bool $includeImageEditorModalJs = false,
        public bool $includeMediaModalJs = false,
        public bool $includeMediaManagerSubmitter = false,
        public bool $includeMediaLabSubmitter = false,
        public bool $includeLiteYoutube = false,
        public bool $includeDebugToggleJs = false,
        public bool $includeTinymceCustomFilePickerIframeJs = false,
        public string $for = 'unknown',
    ) {
        // Default theme from config
        $this->theme ??= config('medialibrary-extensions.frontend_theme', 'plain');

//        Log::info('For: ' .$this->for . ' ' . ($this->includeImageEditorModalJs ? 'Yes' : 'No'));

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
                'mediaLabSubmitter' => $this->includeMediaLabSubmitter,
                'debugToggle' => $this->includeDebugToggleJs,
                'liteYoutube' => $this->includeLiteYoutube,
            ],
            'for' => $this->for, // keep track of which config belongs to what
            'theme' => $this->theme,
            'assetBasePath' => asset(
                config('medialibrary-extensions.asset_path')
            ),
            'imageEditorTranslationsPath' => config('medialibrary-extensions.image_editor_translations_path', '/image-editor-translations/'),

            // TODO don't think this is the right place for trnslations
            // Translation strings (CSP-safe: no inline script)
            'translations' => [
                'csrf_token_mismatch' => __('medialibrary-extensions::http.csrf_token_mismatch'),

                'unauthenticated' => __('medialibrary-extensions::http.unauthenticated'),

                'forbidden' => __('medialibrary-extensions::http.forbidden'),

                'not_found' => __('medialibrary-extensions::http.not_found'),

                'validation_failed' => __('medialibrary-extensions::http.validation_failed'),

                'too_many_requests' => __('medialibrary-extensions::http.too_many_requests'),

                'server_error' => __('medialibrary-extensions::http.server_error'),

                'unknown_error' => __('medialibrary-extensions::http.unknown_error'),

                'medium_replaced' => __('medialibrary-extensions::messages.medium_replaced'),

                'medium_replacement_failed' => __('medialibrary-extensions::messages.medium_replacement_failed'),

                'image_load_failed' => __('medialibrary-extensions::messages.image_load_failed'),

                'upload_disabled_only_one_medium_allowed' => __('medialibrary-extensions::messages.upload_disabled_only_one_medium_allowed'),

                'upload_disabled_max_items_reached' => __('medialibrary-extensions::messages.upload_disabled_max_items_reached'),

                'upload_failed' => __('medialibrary-extensions::messages.upload_failed'),

                'update_failed' => __('medialibrary-extensions::messages.could_not_save_updated_medium'),

                "unable_to_read_response_body" => __('medialibrary-extensions::messages.unable_to_read_response_body'),

                // Template for media counts used by JS; placeholders are replaced client-side
                'media_counts' => __('medialibrary-extensions::messages.media_counts', [
                    'current' => ':current',
                    'total' => ':total',
                ]),
            ],
        ];

//        Log::info('Asset component', [
//            'for' => $this->for,
//            'includeImageEditorModalJs' => $this->includeImageEditorModalJs,
//            'assetConfig' => $this->assetConfig,
//        ]);
    }

    public function render(): View
    {
        return view('medialibrary-extensions::components.shared.assets');
    }
}
