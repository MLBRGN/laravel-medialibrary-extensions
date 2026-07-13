<button type="button"
        {{ $attributes->class([
            'mle-button mle-button-icon',
            'btn btn-primary' => config('medialibrary-extensions.frontend_theme') === 'bootstrap-5'
        ]) }}
        title="{{ __('medialibrary-extensions::messages.debug') }}"
        data-mle-action="debugger-toggle"
>
    <x-mle-shared-icon
            name="{{ config('medialibrary-extensions.icons.bug') }}"
            title="{{ __('medialibrary-extensions::messages.debug') }}"
    />
</button>
