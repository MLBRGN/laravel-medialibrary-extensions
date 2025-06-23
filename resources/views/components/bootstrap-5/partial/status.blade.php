<div class="status-container mle-flex-grow visible" data-status-container>
    <div {{ $attributes->class([
            'mle-status-message', 
            'w-100',
            'alert',
            'alert-dismissible',
            'alert-success' => ($status && $status['type'] === 'success'),
            'alert-danger' => ($status && $status['type'] === 'error'),
        ])->merge() }}
        data-status-message
        data-base-classes="mle-status-message w-100 alert alert-dismissible visible"
        data-success-classes="alert-success"
        data-error-classes="alert-danger"
    >
    @if ($status)
        {{$status['message'] }}
    @endif
    </div>
</div>
