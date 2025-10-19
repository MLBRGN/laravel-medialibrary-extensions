@if($includeCss)
    @once
        <script>
            if (!document.getElementById('mlbrgn-css-{{ $frontendTheme }}')) {
                alert('test');
                const link = document.createElement('link');
                link.id = 'mlbrgn-css-{{ $frontendTheme }}';
                link.rel = 'stylesheet';
                link.href = "{{ asset('vendor/mlbrgn/media-library-extensions/app-'.$frontendTheme.'.css') }}";
                document.head.appendChild(link);
                console.log('css dynamically loaded');
            }
        </script>

        {{-- Fallback for no-JS users, TODO not working even when disabling js, js is enabled in FF, Chrome and Brave --}}
        <noscript>
            <link rel="stylesheet" href="{{ asset('vendor/mlbrgn/media-library-extensions/app-'.$frontendTheme.'.css') }}">
            <div style="color:red;">
                JavaScript is disabled. Some features may not work.
            </div>
        </noscript>
    @endonce
@endif

@if($includeJs)
    @once
        <script type="module">
            if (!window.mlbrgnJsLoaded) {
                const script = document.createElement('script');
                script.type = 'module';
                script.src = "{{ asset('vendor/mlbrgn/media-library-extensions/app-'.$frontendTheme.'.js') }}";
                document.head.appendChild(script);
                window.mlbrgnJsLoaded = true;

                window.mediaLibraryTranslations = {!! json_encode([
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
                ]) !!};
                console.log('css dynamically loaded');
            }
        </script>
        {{-- no fallback needed, if js disabled, this wont work anyway--}}
    @endonce
@endif

@if($includeImageEditorJs)
    @once
        <script type="module" src="{{ asset('vendor/mlbrgn/media-library-extensions/image-editor-listener.js') }}"></script>
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
            // Loading the "lite-YouTube" extension caused issues with the host app,
            // now waiting for dom to be loaded and only
            // loading YT iframe api and "lite-YouTube" when not loaded
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
