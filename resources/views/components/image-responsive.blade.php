<img {{ $attributes->merge(['class' => '']) }}
     src="{{ $url }}"
     @if($srcset)
         srcset="{{ $srcset }}"
     sizes="{{ $sizes }}"
     @endif
     @if($lazy ?? true)
         loading="lazy"
     @endif
     alt="{!! $alt !!}"
>
