@if(app()->environment('local'))
    @if(config('form-components.mle_using_local_package'))
        <span class="mlbrgn-badge-local-package">USING LOCAL PACKAGE</span>
    @else
        <span class="mlbrgn-badge-remote-package">USING REMOTE PACKAGE</span>
    @endif
@endif
