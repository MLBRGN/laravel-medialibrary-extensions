<h1>Flash me</h1>
{{--<pre>{{ var_dump(session()->all()) }}</pre>--}}
@if (session()->has(flash_prefix('status')))
    @if(session(flash_prefix('status'))['target'] === $targetId)
        <p type="{{session(flash_prefix('status'))['type']}}">
            {{ session(flash_prefix('status'))['message'] }}
        </p>
    @endif
@endif

{{--@if (session()->has(flash_prefix('error')))--}}
{{--    <p>--}}
{{--        {{ session(flash_prefix('error')) }}--}}
{{--    </p>--}}
{{--@endif--}}
