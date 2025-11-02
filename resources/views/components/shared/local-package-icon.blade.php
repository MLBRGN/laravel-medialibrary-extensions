@if(config('media-library-extensions.mle_using_local_package'))
    <div
        class="mle-button-pseudo mle-button-icon-pseudo"
        title="{{ __('media-library-extensions::messages.debug') }}"
    >
        @if(config('media-library-extensions.mle_using_local_package'))
            <span class="mle-icon-local-package">L</span>
        @else
            <span class="mle-icon-remote-package">R</span>
        @endif
    </div>
@endif
