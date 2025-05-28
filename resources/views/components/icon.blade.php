@if ($name && $iconExists)
    <x-dynamic-component class="icon" :component="$name" :title="$title"/>
@else
    <span role="img" aria-label="{{ $title }}" title="{{ $title }}">❓</span>
@endif
