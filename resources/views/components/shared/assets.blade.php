{{-- assets.blade.php (CSP-safe, no inline JS) --}}

{{-- Provide JSON config via a data-attribute (CSP safe) --}}
<div id="mlbrgn-asset-config"
     class="mlbrgn-asset-config"
     data-config='@json($config)'
     style="display:none"></div>

{{-- Load the single external asset loader --}}
<script type="module"
        src="{{ asset('vendor/mlbrgn/media-library-extensions/js/shared/dynamic-loader.js') }}">
</script>

{{-- The slot --}}
{{ $slot }}