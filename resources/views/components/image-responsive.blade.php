@if ($url)
    <img
        {{ $attributes->merge(['class' => '']) }}
        src="{{ $url }}"
        @if ($srcset) srcset="{{ $srcset }}" @endif
        @if ($srcset && $sizes) sizes="{{ $sizes }}" @endif
        alt="{{ $alt }}"
        @if ($lazy) loading="lazy" @endif
        data-mle-image
    >
@else
    <img
        {{ $attributes->merge(['class' => '']) }}
        src="{{ asset('vendor/media-library-extensions/images/fallback.png') }}"
        alt="Missing image"
        class="opacity-50"
        data-mle-image
    >
@endif
