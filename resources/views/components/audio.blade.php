<div 
    {{ $attributes->class('mle-audio') }}
    id="{{ $getDomId() }}"
    data-mle-audio-container
>
    <div class="mle-audio-preview">
        <div class="mle-audio-info">
            <p>
                {{ Str::limit($medium->file_name, 15) }}
            </p>
        </div>
{{--        TODO $getDomId() used twice in file !--}}
        <audio id="{{ $getDomId() }}" controls data-mle-audio>
            <source src="{{ $medium->getUrl() }}" type="{{ $medium->mime_type }}">
            {{ __('medialibrary-extensions::messages.your_browser_does_not_support_the_audio_element') }}
        </audio>
    </div>
</div>