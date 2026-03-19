{{-- assets.blade.php --}}
@php($nonce = mlbrgn_csp_nonce())

{{--    <div id="mlbrgn-asset-config"--}}
{{--         class="mlbrgn-asset-config"--}}
{{--         data-config='@json($config)'--}}
{{--         style="display:none"></div>--}}

    {{-- CSP-safe JSON config for JS loader --}}
    <script type="application/json"
            class="mlbrgn-asset-config"
            @isset($nonce) nonce="{{ $nonce }}" @endisset
    >
        @json($config)
    </script>

    @once
    {{-- Load the asset loader --}}
        <script type="module"
                src="{{ asset('vendor/mlbrgn/media-library-extensions/js/shared/dynamic-loader.js') }}"
                @isset($nonce) nonce="{{ $nonce }}" @endisset>
        </script>
    @endonce

{{-- The slot --}}
{{ $slot }}