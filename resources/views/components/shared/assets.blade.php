@php
    $nonce = mlbrgn_csp_nonce();
@endphp

{{-- 1) Global config (once): base path + theme (+ optional translations) --}}
@once
    <script
        id="mle-global"
        type="application/json"
        class="mlbrgn-medialibrary-config"
        @isset($nonce) nonce="{{ $nonce }}" @endisset
    >{!! json_encode([
        'for' => 'global',
        'theme' => $assetConfig['theme'] ?? config('medialibrary-extensions.frontend_theme', 'plain'),
        'assetBasePath' => asset(config('medialibrary-extensions.asset_path')),
        // Keep global translations here if you want them available immediately
        'translations' => $assetConfig['translations'] ?? [],
        'assets' => [], // no assets at global level
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endonce

{{-- 2) Widget config (many): assets only, no base path/theme duplication --}}
@php
    $widgetConfig = $assetConfig;
    unset($widgetConfig['assetBasePath'], $widgetConfig['theme']);
@endphp

@if(!empty($widgetConfig))
    <script
        id="{{ $assetConfig['for'] }}"
        type="application/json"
        class="mlbrgn-medialibrary-config"
        @isset($nonce) nonce="{{ $nonce }}" @endisset
    >{!! json_encode($widgetConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endif

{{-- 3) Loader (once) --}}
@once
    <script
        type="module"
        src="{{ asset(config('medialibrary-extensions.asset_path') . '/js/core/media-library-loader.js') }}"
        @isset($nonce) nonce="{{ $nonce }}" @endisset
    ></script>
@endonce

{{ $slot }}
{{--@php--}}
{{--    $nonce = mlbrgn_csp_nonce();--}}
{{--@endphp--}}

{{--@if(!empty($assetConfig))--}}
{{--    <script--}}
{{--        id="{{ $assetConfig['for'] }}"--}}
{{--        type="application/json"--}}
{{--        class="mlbrgn-medialibrary-config"--}}
{{--        @isset($nonce) nonce="{{ $nonce }}" @endisset--}}
{{--    >{!! json_encode([--}}
{{--        ...$assetConfig,--}}
{{--    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>--}}
{{--@endif--}}

{{--@once--}}
{{--    <script--}}
{{--        type="module"--}}
{{--        src="{{ asset(config('medialibrary-extensions.asset_path') . '/js/core/media-library-loader.js') }}"--}}
{{--        @isset($nonce) nonce="{{ $nonce }}" @endisset--}}
{{--    ></script>--}}
{{--@endonce--}}

{{--{{ $slot }}--}}