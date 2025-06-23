@if ($name && $iconExists)
    <span {{ $attributes->merge(['class' => 'mle-icon-container']) }} role="img" aria-label="{{ $title }}" title="{{ $title }}">
        <x-dynamic-component 
            :component="$name" 
            :title="$title"/>
    </span>
@else
    <span 
        role="img" 
        aria-label="{{ $title }}" 
        title="{{ $title }}">❓</span>
@endif
