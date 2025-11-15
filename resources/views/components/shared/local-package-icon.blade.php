@if(config('media-library-extensions.mle_using_local_package'))
    <button
        @class([
        'mle-button-pseudo', 
        'mle-button-icon-pseudo',
        'mle-button-no-hover',
        'btn btn-primary' => config('media-library-extensions.frontend_theme') === 'bootstrap-5'
    ])
        title="{{ __('media-library-extensions::messages.debug') }}"
    >
        @if(config('media-library-extensions.mle_using_local_package'))
            <span class="mle-icon-local-package">L</span>
        @else
            <span class="mle-icon-remote-package">R</span>
        @endif
    </button>
@endif
