<div class="status-container mle-flex-grow" data-status-container>
    <div {{ $attributes->class([
            'mle-status-message', 
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
