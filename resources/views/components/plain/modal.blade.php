<div
    {{ $attributes->merge(['class' => 'modal']) }}
    id="{{ $modalId }}"
    tabindex="-1"
    aria-labelledby="{{ $modalId }}-title"
    aria-hidden="true">
    <div class="modal-dialog modal-almost-fullscreen">
        <div class="modal-content">
            @if ($showHeader === true)
                <div class="modal-header">
                    <h1 
                        class="h2 modal-title" 
                        id="{{ $modalId }}-title">{{ $title }}</h1>
                    <button 
                        type="button" 
                        class="" 
                        data-bs-dismiss="modal" 
                        aria-label="Sluit"
                    ></button>
                </div>
            @else
                <h1 
                    class="modal-title visually-hidden" 
                    id="{{ $modalId }}-title">{{ $title }}</h1>
            @endif
            @if ($showBody === true)
                <div class="modal-body {{ $noPadding ? 'p-0' : ''}}">
                    {{ $slot }}
                </div>
            @else
                {{ $slot }}
            @endif
        </div>
    </div>
</div>
@once
    <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app.css') }}">
@endonce
