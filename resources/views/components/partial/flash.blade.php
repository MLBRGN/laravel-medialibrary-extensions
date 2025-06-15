@if ($status)
    <div {{ $attributes->class([
            'mle-status-message', 
            'mle-status-message-'.$status['type'],
            $extraClasses
        ])->merge() }}>
        {{$status['message'] }}
    </div>
@endif
