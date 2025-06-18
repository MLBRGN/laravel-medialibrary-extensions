@if ($status)
    <div {{ $attributes->class([
            'mle-status-message', 
            'mle-status-message-'.$status['type'],
            'w-100',
            'alert',
            'alert-dismissible',
            'alert-success' => $status['type'] === 'success',
            'alert-danger' => $status['type'] === 'error',
        ])->merge() }}
        data-status-message
    >
        {{$status['message'] }}
    </div>
@endif
