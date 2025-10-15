@if($includeCss)
    @once
        <link rel="stylesheet" href="{{ asset('vendor/mlbrgn/media-library-extensions/app-'.$frontendTheme.'.css') }}">
{{--        <link rel="stylesheet" href="{{ asset('vendor/mlbrgn/media-library-extensions/app-plain.css') }}">--}}
    @endonce
@endif

@if($includeJs)
    @once
        <script type="module" src="{{ asset('vendor/mlbrgn/media-library-extensions/app-'.$frontendTheme.'.js') }}"></script>
        @php
            $translations = [
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
            ];
        @endphp
        <script>
            window.mediaLibraryTranslations = {!! json_encode($translations) !!};
        </script>
    @endonce
@endif

@if($includeImageEditorJs)
    @once
        <script type="module" src="{{ asset('vendor/mlbrgn/media-library-extensions/image-editor.js') }}"></script>
    @endonce
@endif

@if($includeFormSubmitter)
    @once
        <script type="module" src="{{ asset('vendor/mlbrgn/media-library-extensions/form-submitter.js') }}"></script>
    @endonce
@endif

@if($includeLiteYoutube)
    @once
        <script type="module">
            // Loading the "lite-youtube" extension caused issues with the host app,
            // now waiting for dom to be loaded and only
            // loading YT iframe api and "lite-youtube" when not loaded
            // already (by host app)
            document.addEventListener('DOMContentLoaded', function() {
                if (!window.YT) {
                    const tag = document.createElement('script');
                    tag.src = "https://www.youtube.com/iframe_api";
                    document.head.appendChild(tag);
                }

                if (!customElements.get('lite-youtube')) {
                    console.log('no lite-youtube, loading');
                    const script = document.createElement('script');
                    script.src = "{{ asset('vendor/mlbrgn/media-library-extensions/lite-youtube.js') }}";
                    document.head.appendChild(script);
                } else {
                    console.log('lite-youtube already present');
                }
            })
        </script>
    @endonce
@endif

{{ $slot }}
