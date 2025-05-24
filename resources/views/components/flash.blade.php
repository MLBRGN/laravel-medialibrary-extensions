@if ($status)
    <p type="{{ $status['type'] }}">
        {{ $status['message'] }}
    </p>
@endif
