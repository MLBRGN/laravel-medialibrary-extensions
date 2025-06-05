BS 5
@if($includeCss)
    @once
        <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app.css') }}">
    @endonce
@endif

@if($includeJs)
    @once
        <script src="{{ asset('vendor/media-library-extensions/app.js') }}"></script>
    @endonce
@endif

@if($includeYoutubeIframeApi)
    @once
        <script src="https://www.youtube.com/iframe_api"></script>
    @endonce
@endif

{{ $slot }}

{{--<div {{ $attributes->merge(['class' => 'mlbrgn-mle-component']) }} id="{{ $id }}">--}}

{{--    <!-- Your component content here -->--}}

{{--    <script>--}}
{{--        (function() {--}}
{{--            if (!window.__mle_assets_loaded) {--}}
{{--                window.__mle_assets_loaded = true;--}}

{{--                // Load CSS--}}
{{--                var link = document.createElement('link');--}}
{{--                link.rel = 'stylesheet';--}}
{{--                link.href = "{{ asset('vendor/media-library-extensions/app.css') }}";--}}
{{--                document.head.appendChild(link);--}}

{{--                // Load JS--}}
{{--                var script = document.createElement('script');--}}
{{--                script.src = "{{ asset('vendor/media-library-extensions/app.js') }}";--}}
{{--                script.defer = true;  // avoid blocking render--}}
{{--                document.head.appendChild(script);--}}
{{--            }--}}
{{--        })();--}}
{{--    </script>--}}
{{--</div>--}}
