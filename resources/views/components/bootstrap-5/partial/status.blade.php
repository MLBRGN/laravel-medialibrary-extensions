<div 
    class="status-container mle-flex-grow visible" 
    data-mle-status-container 
    data-mle-status-timeout="{{ config('media-library-extensions.status_message_timeout', 5000) }}"
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
    @if(!config('media-library-extensions.use_xhr'))
        <script>
            document.querySelectorAll('[data-mle-status-message]').forEach(el => {
                setTimeout(() => {
                    el.classList.add('hidden');
                }, {{ config('media-library-extensions.status_message_timeout', 5000) }});
            });
        </script>
    @endif
</div>
