<div {{ $attributes->class('mle-audio') }}>
    <div class="mle-audio-preview">
        <div class="mle-audio-info">
            <p>
                {{ Str::limit($medium->file_name, 15) }}
            </p>
        </div>
        <audio controls data-mle-audio>
            <source src="{{ $medium->getUrl() }}" type="{{ $medium->mime_type }}">
            {{ __('media-library-extensions::messages.your_browser_does_not_support_the_audio_element') }}
        </audio>
    </div>
</div>