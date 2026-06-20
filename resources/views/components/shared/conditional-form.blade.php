@props([
    'formAttributes' => [],
    'divAttributes' => [],
])
@if($useXhr)
    <div {{ $attributes->merge($divAttributes)->class(['mle-conditional-form']) }} data-xhr-method="{{ $method }}">
        {{ $slot }}
    </div>
@else
    <form {{ $attributes->merge($formAttributes)->class(['mle-conditional-form']) }} enctype="multipart/form-data" method="{{ $method }}">
        @csrf
        @if(!in_array(strtolower($method), ['get', 'post']))
            @method($method)
        @endif
        {{ $slot }}
    </form>
@endif
