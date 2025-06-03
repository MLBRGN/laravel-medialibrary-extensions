@if ($name && $iconExists)
    <x-dynamic-component 
        {{ $attributes->merge(['class' => 'mle-icon']) }}
        :component="$name" 
        :title="$title"/>
@else
    <span 
        role="img" 
        aria-label="{{ $title }}" 
        title="{{ $title }}">❓</span>
@endif
