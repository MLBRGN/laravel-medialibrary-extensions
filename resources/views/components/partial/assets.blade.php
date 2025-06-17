@if($includeCss)
    @once
        <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app-'.$theme.'.css') }}">
    @endonce
@endif

@if($includeJs)
    @once
        <script src="{{ asset('vendor/media-library-extensions/app-'.$theme.'.js') }}"></script>
    @endonce
@endif

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
