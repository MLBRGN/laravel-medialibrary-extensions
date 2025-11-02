<div 
    class="status-container mle-flex-grow visible" 
    data-mle-status-container
    data-mle-status-timeout="{{ config('media-library-extensions.status_message_timeout', 5000) }}"
>
    <div {{ $attributes->class([
            'mle-status-message', 
            'mle-status-message-success' => ($status && $status['type'] === 'success'),
            'mle-status-message-error' => ($status && $status['type'] === 'error'),
        ])->merge() }}
        data-mle-status-message 
        data-mle-base-classes="mle-status-message"
        data-mle-success-classes="mle-status-message-success"
        data-mle-error-classes="mle-status-message-error"
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
