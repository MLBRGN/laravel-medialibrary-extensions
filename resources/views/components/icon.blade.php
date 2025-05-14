{{--@dd($title)--}}
@if ($name && $iconExists)
    <x-dynamic-component :component="$name" :title="$title"/>
@else
    <span role="img" aria-label="{{ $title }}" title="{{ $title }}">❓
@endif


{{--@php--}}
{{--    try {--}}
{{--        app(ComponentTagCompiler::class)--}}
{{--            ->componentResolver()--}}
{{--            ->resolve('bi-bell-fill');--}}
{{--        $iconExists = true;--}}
{{--    } catch (Throwable) {--}}
{{--        $iconExists = false;--}}
{{--    }--}}
{{--dump($iconExists);--}}
{{--@endphp--}}

{{--@if ($iconExists)--}}
{{--    has--}}
{{--    --}}{{-- Icons are available --}}
{{--    <x-dynamic-component :component="$name" :title="$title" {{ $attributes }} />--}}
{{--@else--}}
{{--    --}}{{-- Show a warning, or skip icon usage --}}
{{--    does not exist--}}
{{--    @if($fallback)--}}
{{--        <span role="img" aria-label="{{ $title ?? 'icon' }}" title="{{ $title }}">--}}
{{--            {{ $fallback }}--}}
{{--        </span>--}}
{{--    @endif--}}
{{--@endif--}}


{{--@php use BladeUI\Icons\Components\Icon; @endphp--}}
{{--@props(['name'])--}}

{{--@if (component_exists($name) && $name)--}}
{{--    <x-dynamic-component :component="$name" {{ $attributes }} />--}}
{{--@else--}}
{{--    --}}{{-- Optional fallback --}}
{{--    <span {{ $attributes }}>★</span>--}}
{{--@endif--}}


{{--@php--}}
{{--    use BladeUI\Icons\Components\Icon;--}}
{{--@endphp--}}

{{--@props([--}}
{{--    'name',--}}
{{--    'title' => null,--}}
{{--    'ariaLabel' => null,--}}
{{--    'decorative' => false,--}}
{{--])--}}

{{--@php--}}
{{--    $attributes = $attributes->merge([--}}
{{--        'role' => $decorative ? 'presentation' : 'img',--}}
{{--        'aria-hidden' => $decorative ? 'true' : 'false',--}}
{{--    ]);--}}

{{--    if ($ariaLabel) {--}}
{{--        $attributes = $attributes->merge(['aria-label' => $ariaLabel]);--}}
{{--    }--}}
{{--@endphp--}}

{{--@if (class_exists(Icon::class) && $name)--}}
{{--    <x-dynamic-component :component="$name" {{ $attributes }}>--}}
{{--        @if($title)--}}
{{--            <title>{{ $title }}</title>--}}
{{--        @endif--}}
{{--    </x-dynamic-component>--}}
{{--@elseif (!class_exists(Icon::class))--}}
{{--    <span {{ $attributes }}>--}}
{{--        ⚠️ {{ $title ?? 'Icon set not installed' }}--}}
{{--    </span>--}}
{{--@else--}}
{{--    <span {{ $attributes }}>--}}
{{--        ⭐ {{ $title ?? '' }}--}}
{{--    </span>--}}
{{--@endif--}}
