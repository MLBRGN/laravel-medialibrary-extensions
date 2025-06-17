@if ($status)
    <div {{ $attributes->class([
            'mle-status-message', 
            'mle-status-message-'.$status['type'],
            $extraClasses
        ])->merge() }}
        data-status-message
    >
        {{$status['message'] }}
    </div>
@endif
