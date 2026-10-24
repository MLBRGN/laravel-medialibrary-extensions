<div 
    {{ $attributes->class(['mle-spinner-container', 'alert', 'alert-info']) }} 
    data-mle-spinner-container
    id="{{ $getDomId() }}"
>
    <div class="mle-spinner"></div>
    <span class="mle-spinner-text"
        data-mle-spinner-text
    >{{ __('medialibrary-extensions::messages.please_wait') }}</span>
</div>
