@php($nonce = mlbrgn_csp_nonce())

@if(!empty($assetConfig))
    <script
        type="application/json"
        class="mlbrgn-medialibrary-config"
        @isset($nonce) nonce="{{ $nonce }}" @endisset
    >{!! json_encode([
        ...$assetConfig,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endif

@once
    <script
        type="module"
        src="{{ asset('vendor/mlbrgn/media-library-extensions/js/core/media-library-loader.js') }}"
        @isset($nonce) nonce="{{ $nonce }}" @endisset
    ></script>
@endonce

{{ $slot }}