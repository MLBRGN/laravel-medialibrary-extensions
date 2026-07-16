@php
    $nonce = mlbrgn_csp_nonce();
@endphp

@if(!empty($assetConfig))
    <script
        id="{{ $assetConfig['for'] }}"
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
        src="{{ asset(config('medialibrary-extensions.asset_path') . '/js/core/media-library-loader.js') }}"
        @isset($nonce) nonce="{{ $nonce }}" @endisset
    ></script>
@endonce

{{ $slot }}