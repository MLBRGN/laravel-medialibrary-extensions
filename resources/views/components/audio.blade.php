<div class="media-manager-audio-preview">
    <audio controls>
        <source src="{{ $medium->getUrl() }}" type="{{ $medium->mime_type }}">
        {{ __('media-library-extensions::messages.your_browser_does_not_support_the_audio_element') }}
    </audio>
</div>