<div 
    class="mle-status-container mle-flex-grow visible" 
    data-mle-status-container 
    data-mle-status-timeout="{{ config('medialibrary-extensions.status_message_timeout', 5000) }}"
    id="{{ $getDomId() }}}"
>
    <div {{ $attributes->class([
            'mle-status-message', 
            'alert',
            'alert-dismissible',
            'alert-success' => ($status && $status['type'] === 'success'),
            'alert-danger' => ($status && $status['type'] === 'error'),
        ])->merge() }}
        data-mle-status-message
        data-mle-base-classes="mle-status-message alert alert-dismissible visible"
        data-mle-success-classes="alert-success"
        data-mle-error-classes="alert-danger"
    >
        @if ($status)
            {{$status['message'] }}
        @endif
    </div>
    @if(!config('medialibrary-extensions.use_xhr'))
        @php
            $nonce = mlbrgn_csp_nonce();
        @endphp
        <script
            @isset($nonce) nonce="{{ $nonce }}" @endisset
        >
            document.querySelectorAll('[data-mle-status-container]').forEach(el => {
                setTimeout(() => {
                    el.classList.remove('visible');
                }, {{ config('medialibrary-extensions.status_message_timeout', 5000) }});
            });
        </script>
    @endif
</div>
