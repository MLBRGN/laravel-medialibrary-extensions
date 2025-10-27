@if(config('media-library-extensions.mle_using_local_package'))
    <button
        type="button"
        class="mle-button mle-button-icon btn btn-primary"
        title="{{ __('media-library-extensions::messages.debug') }}"
    >
        @if(config('media-library-extensions.mle_using_local_package'))
            <span class="mlbrgn-icon-local-package">L</span>
        @else
            <span class="mlbrgn-icon-remote-package">R</span>
        @endif
    </button>
@endif
