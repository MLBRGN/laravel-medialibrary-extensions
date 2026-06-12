@if ($url)
    <img
        {{ $attributes->merge(['class' => '']) }}
        src="{{ $url ?: $placeholder }}"
        @if ($srcset) srcset="{{ $srcset }}" @endif
        @if ($srcset && $sizes) sizes="{{ $sizes }}" @endif
        alt="{{ $alt }}"
        @if ($lazy) loading="lazy" @endif
        data-mle-image
        data-test="media-preview-item"
    >
@else
    <img
        {{ $attributes->merge(['class' => '']) }}
        src="{{ $placeholder }}"
        alt="Missing image"
        class="mle-opacity-50"
        data-mle-image
        data-test="media-preview-item"
    >
@endif
