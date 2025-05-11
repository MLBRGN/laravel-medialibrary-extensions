<img src="{{ $media->getUrl($conversion) }}"
     srcset="{{ $media->getSrcset($conversion) }}"
     sizes="{{ $sizes }}"
     {{ $attributes->merge(['class' => '']) }}
     @if($lazy ?? true) loading="lazy" @endif/>

