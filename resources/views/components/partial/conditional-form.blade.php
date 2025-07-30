@props([
    'useXhr' => false,
    'formAttributes' => [],
    'divAttributes' => [],
    'method' => 'post'
])
@if($useXhr)
    <div {{ $attributes->merge($divAttributes)->class(['conditional-form']) }}>
        {{ $slot }}
    </div>
@else
    <form {{ $attributes->merge($formAttributes)->class(['conditional-form']) }} enctype="multipart/form-data" method="post">
        @csrf
{{--        @if ($method !== 'post')--}}
{{--            @method($method)--}}
{{--        @endif--}}
        {{ $slot }}
    </form>
@endif
