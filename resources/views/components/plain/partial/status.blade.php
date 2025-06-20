<div class="status-container mle-flex-grow visible" data-status-container>
    <div {{ $attributes->class([
            'mle-status-message', 
            'mle-status-message-success' => ($status && $status['type'] === 'success'),
            'mle-status-message-error' => ($status && $status['type'] === 'error'),
        ])->merge() }}
        data-status-message 
        data-base-classes="mle-status-message"
        data-success-classes="mle-status-message-success"
        data-error-classes="mle-status-message-error"
    >
    @if ($status)
        {{$status['message'] }}
    @endif
    </div>
</div>
