@props([
    'useXhr' => false,
    'formAttributes' => [],
    'divAttributes' => [],
])

@if($useXhr)
    <div {{ $attributes->merge($divAttributes)->class(['conditional-form']) }}>
        {{ $slot }}
    </div>
@else
    <form {{ $attributes->merge($formAttributes)->class(['conditional-form']) }} enctype="multipart/form-data" method="post">
        {{ $slot }}
    </form>
@endif
