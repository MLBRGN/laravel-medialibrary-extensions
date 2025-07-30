@props([
    'useXhr' => false,
    'formAttributes' => [],
    'divAttributes' => [],
    'method' => 'post'
])
@if($useXhr)
    <div {{ $attributes->merge($divAttributes)->class(['conditional-form']) }} data-xhr-method="{{ $method }}">
        {{ $slot }}
    </div>
@else
    <form {{ $attributes->merge($formAttributes)->class(['conditional-form']) }} enctype="multipart/form-data" method="{{ $method }}">
        @csrf
        {{ $slot }}
    </form>
@endif
