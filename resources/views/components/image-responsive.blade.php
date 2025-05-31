{{--TODO class add possibility--}}
<img
    src="{{ $hasGeneratedConversion ? $medium->getUrl($useConversion) : $medium->getUrl() }}"

    @if($hasGeneratedConversion)
        srcset="{{ $medium->getSrcset($useConversion) }}"
        sizes="{{ $sizes }}"
    @endif
    {{ $attributes->merge(['class' => '']) }}
    @if($lazy ?? true)
        loading="lazy"
    @endif
    alt="{!! $alt !!}"
>
