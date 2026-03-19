{{-- assets.blade.php --}}
@php($nonce = mlbrgn_csp_nonce())

@once
    {{-- Provide JSON config via a data-attribute (CSP safe) --}}
    <div id="mlbrgn-asset-config"
         class="mlbrgn-asset-config"
         data-config='@json($config)'
         style="display:none"></div>

    {{-- CSP-safe JSON config for JS loader --}}
{{--    TODO--}}
{{--    <script type="application/json"--}}
{{--            id="mlbrgn-asset-config"--}}
{{--            @if($nonce) nonce="{{ $nonce }}" @endif--}}
{{--    >--}}
{{--        @json($config)--}}
{{--    </script>--}}
    
    {{-- Load the asset loader --}}
    <script type="module"
            src="{{ asset('vendor/mlbrgn/media-library-extensions/js/shared/dynamic-loader.js') }}"
            @isset($nonce) nonce="{{ $nonce }}" @endisset>
    </script>
@endonce

{{-- The slot --}}
{{ $slot }}