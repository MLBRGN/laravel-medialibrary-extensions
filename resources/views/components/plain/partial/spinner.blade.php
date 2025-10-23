{{--<div {{ $attributes->class(['mle-spinner-container']) }} data-spinner-container style="opacity:0; max-height:0; overflow:hidden;">--}}
<div {{ $attributes->class(['mle-spinner-container']) }} data-spinner-container>
    <div class="mle-spinner"></div>
    <span class="mle-spinner-text">{{ __('media-library-extensions::messages.please_wait') }}</span>
</div>
