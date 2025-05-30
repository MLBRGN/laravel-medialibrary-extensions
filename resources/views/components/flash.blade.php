@if ($status)
    <p class="status-{{ $status['type'] }}">
        {{ $status['message'] }}
    </p>
@endif
