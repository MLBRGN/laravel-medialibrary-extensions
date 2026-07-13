@if(config('medialibrary-extensions.mle_using_local_package'))
    <button
        @class([
        'mle-button-pseudo', 
        'mle-button-icon-pseudo',
        'mle-button-no-hover',
        'btn btn-primary' => config('medialibrary-extensions.frontend_theme') === 'bootstrap-5'
    ])
        title="{{ __('medialibrary-extensions::messages.debug') }}"
    >
        @if(config('medialibrary-extensions.mle_using_local_package'))
            <span class="mle-icon-local-package">L</span>
        @else
            <span class="mle-icon-remote-package">R</span>
        @endif
    </button>
@endif
