<img
    src="{{ $hasGeneratedConversion ? $medium->getUrl($conversion) : $medium->getUrl() }}"

    @if($hasGeneratedConversion)
        srcset="{{ $medium->getSrcset($conversion) }}"
        sizes="{{ $sizes }}"
    @endif
    {{ $attributes->merge(['class' => '']) }}
    @if($lazy ?? true)
        loading="lazy"
    @endif
    alt="{!! $alt !!}"
/>
