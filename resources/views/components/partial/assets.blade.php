@if($includeCss)
    @once
        <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app-'.$frontendTheme.'.css') }}">
    @endonce
@endif

@if($includeJs)
    @once
        <script type="module" src="{{ asset('vendor/media-library-extensions/app-'.$frontendTheme.'.js') }}"></script>
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
            ];
        @endphp
        <script>
            window.mediaLibraryTranslations = {!! json_encode($translations) !!};
        </script>
    @endonce
@endif

{{--@if($includeImageEditorJs)--}}
{{--    @once--}}
{{--        <script type="module" src="{{ asset('vendor/media-library-extensions/image-editor.js') }}"></script>--}}
{{--    @endonce--}}
{{--@endif--}}

@if($includeFormSubmitter)
    @once
        <script src="{{ asset('vendor/media-library-extensions/form-submitter.js') }}"></script>
    @endonce
@endif

@if($includeYoutubePlayer)
    @once
        <script src="https://www.youtube.com/iframe_api"></script>
        <script>
            if (!customElements.get('lite-youtube')) {
                const script = document.createElement('script');
                script.src = "{{ asset('vendor/media-library-extensions/lite-youtube.js') }}";
                document.head.appendChild(script);
            }
        </script>
    @endonce
@endif

{{ $slot }}
