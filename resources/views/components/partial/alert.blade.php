@if ($status)
    <div {{ $attributes->class([
            'mle-status-message', 
            'mle-status-message-'.$status['type'],
            $extraClasses
        ])->merge() }}
        id="{{ $id }}"
        data-status-message
    >
        {{$status['message'] }}
    </div>
@endif
