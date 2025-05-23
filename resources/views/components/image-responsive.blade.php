<img
    @if($hasGeneratedConversion)
        src="{{ $medium->getUrl($conversion) }}"
        srcset="{{ $medium->getSrcset($conversion) }}"
        sizes="{{ $sizes }}"
    @else
        src="{{ $medium->getUrl() }}"
    @endif
    {{ $attributes->merge(['class' => '']) }}
    @if($lazy ?? true)
        loading="lazy"
    @endif
    alt="{!! $alt !!}"
/>
