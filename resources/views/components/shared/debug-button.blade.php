<button type="button"
        {{ $attributes->class(['mle-button mle-button-icon']) }}
        title="{{ __('media-library-extensions::messages.debug') }}"
        data-mle-action="debugger-toggle"
>
    <x-mle-shared-icon
            name="{{ config('media-library-extensions.icons.bug') }}"
            title="{{ __('media-library-extensions::messages.debug') }}"
    />
</button>
