@if ($status)
    <div {{ $attributes->class([
            'mle-status-message', 
            'mle-status-message-'.$status['type'],
            'mle-status-message-success' => $status['type'] === 'success',
            'mle-status-message-error' => $status['type'] === 'error',
        ])->merge() }}
        data-status-message
    >
        {{$status['message'] }}
    </div>
@endif
