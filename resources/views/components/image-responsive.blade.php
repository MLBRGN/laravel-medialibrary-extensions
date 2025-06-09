@if ($url)
    <img
        src="{{ $url }}"
        srcset="{{ $srcset }}"
        sizes="{{ $sizes }}"
        alt="{{ $alt }}"
        @if ($lazy) loading="lazy" @endif
    >
@else
    <img
        src="{{ asset('vendor/media-library-extensions/images/fallback.png') }}"
        alt="Missing image"
        class="opacity-50"
    >
@endif
