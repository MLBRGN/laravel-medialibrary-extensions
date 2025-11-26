{{-- Global CSS include --}}
@if($includeCss)
    @once
        <script>
            if (!document.getElementById('mlbrgn-css-{{ $frontendTheme }}')) {
                const link = document.createElement('link');
                link.id = 'mlbrgn-css-{{ $frontendTheme }}';
                link.rel = 'stylesheet';
                link.href = "{{ asset('vendor/mlbrgn/media-library-extensions/css/app-'.$frontendTheme.'.css') }}";
                document.head.appendChild(link);
                console.log('CSS dynamically loaded');
            }
        </script>

        {{-- Fallback for users with JS disabled --}}
        <noscript>
            <link rel="stylesheet" href="{{ asset('vendor/mlbrgn/media-library-extensions/css/app-'.$frontendTheme.'.css') }}">
            <div style="color:red;">
                JavaScript is disabled. Some features may not work.
            </div>
        </noscript>
    @endonce
@endif


{{-- Global JS include --}}
@if($includeJs)
    @once
        <script type="module">
            if (!window.mlbrgnJsLoaded) {
                const script = document.createElement('script');
                script.type = 'module';
                script.src = "{{ asset('vendor/mlbrgn/media-library-extensions/js/root/app-'.$frontendTheme.'.js') }}";
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

                console.log('Main JS dynamically loaded');
            }
        </script>
    @endonce
@endif


{{-- Carousel JS --}}
@if($includeCarouselJs && $frontendTheme === 'plain')
    <script type="module">
        if (!window.mleCarouselJsLoaded) {
            const script = document.createElement('script');
            script.type = 'module';
            script.src = "{{ asset('vendor/mlbrgn/media-library-extensions/js/plain/media-carousel.js') }}";
            document.head.appendChild(script);
            window.mleCarouselJsLoaded = true;

            console.log('mleCarouselJsLoaded');
        }
    </script>
@endif


{{-- TinyMCE file picker --}}
@if($includeTinymceCustomFilePickerIframeJs)
    <script type="module">
        if (!window.tinymceCustomFilePickerIframeJsLoaded) {
            const script = document.createElement('script');
            script.type = 'module';
            script.src = "{{ asset('vendor/mlbrgn/media-library-extensions/js/shared/tinymce-custom-file-picker-iframe.js') }}";
            document.head.appendChild(script);
            window.tinymceCustomFilePickerIframeJsLoaded = true;

            console.log('tinymceCustomFilePickerIframeJsLoaded');
        }
    </script>
@endif


{{-- Image Editor Modal --}}
@if($includeImageEditorModalJs)
    <script type="module">
        if (!window.mleImageEditorModalJs) {
            const script = document.createElement('script');
            script.type = 'module';
            script.src = "{{ asset('vendor/mlbrgn/media-library-extensions/js/' . $frontendTheme . '/modal-image-editor.js') }}";
            document.head.appendChild(script);
            window.mleImageEditorModalJs = true;

            console.log('mleImageEditorModalJs');
        }
    </script>
@endif


{{-- Media Modal --}}
@if($includeMediaModalJs)
    <script type="module">
        if (!window.mleMediaModalJs) {
            const script = document.createElement('script');
            script.type = 'module';
            script.src = "{{ asset('vendor/mlbrgn/media-library-extensions/js/' . $frontendTheme . '/modal-media.js') }}";
            document.head.appendChild(script);
            window.mleMediaModalJs = true;

            console.log('mleMediaModalJs');
        }
    </script>
@endif


{{-- Image Editor Listener --}}
@if($includeImageEditorJs)
    <script type="module">
        if (!window.mleImageEditorListenerJs) {
            const script = document.createElement('script');
            script.type = 'module';
            script.src = "{{ asset('vendor/mlbrgn/media-library-extensions/js/shared/image-editor-listener.js') }}";
            document.head.appendChild(script);
            window.mleImageEditorListenerJs = true;

            console.log('mleImageEditorListenerJs');
        }
    </script>
@endif


{{-- Form Submitter --}}
@if($includeMediaManagerSubmitter)
    <script type="module">
        if (!window.mleFormSubmitterJs) {
            const script = document.createElement('script');
            script.type = 'module';
            script.src = "{{ asset('vendor/mlbrgn/media-library-extensions/js/shared/media-manager-submitter.js') }}";
            document.head.appendChild(script);
            window.mleFormSubmitterJs = true;

            console.log('mleFormSubmitterJs');
        }
    </script>
@endif

@if($includeMediaManagerLabSubmitter)
    <script type="module">
        if (!window.mleMediaLabSubmitterJs) {
            const script = document.createElement('script');
            script.type = 'module';
            script.src = "{{ asset('vendor/mlbrgn/media-library-extensions/js/shared/media-manager-lab-submitter.js') }}";
            document.head.appendChild(script);
            window.mleMediaLabSubmitterJs = true;

            console.log('mleMediaLabSubmitterJs');
        }
    </script>
@endif

{{-- Lite YouTube --}}
@if($includeLiteYoutube)
    @once
        <script type="module">
            document.addEventListener('DOMContentLoaded', function() {
                if (!window.YT) {
                    const tag = document.createElement('script');
                    tag.src = "https://www.youtube.com/iframe_api";
                    document.head.appendChild(tag);
                }

                if (!customElements.get('lite-youtube')) {
                    console.log('no lite-youtube, loading');
                    const script = document.createElement('script');
                    script.src = "{{ asset('vendor/mlbrgn/media-library-extensions/js/shared/lite-youtube.js') }}";
                    document.head.appendChild(script);
                } else {
                    console.log('lite-youtube already present');
                }
            });
        </script>
    @endonce
@endif

{{-- Component slot --}}
{{ $slot }}