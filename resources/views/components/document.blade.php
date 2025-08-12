<div {{ $attributes->class('mle-document') }}>
    <div class="mle-document-preview">
        @if($medium instanceof TemporaryUpload)
{{--            <a href="{{ $medium->getUrl() }}" target="_blank">--}}
                <x-mle-partial-icon
                    :name="$icon['name']"
                    :title="$icon['title']"
                />
{{--            </a>--}}
        @else
{{--            <a href="{{ $medium->getUrl() }}" target="_blank">--}}
                <x-mle-partial-icon
                    :name="$icon['name']"
                    :title="$icon['title']"
                />
{{--            </a>--}}
        @endif
    </div>
</div>
