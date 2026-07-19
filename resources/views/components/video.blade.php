<div 
    {{ $attributes->class('mle-video') }}
    id="{{ $getDomId() }}"
    data-mle-video-container
>
    <div class="mle-video-preview">
        <div class="mle-video-info">
            <p>
                {{ Str::limit($medium->file_name, 15) }}
            </p>
        </div>
        {{--        TODO $getDomId() used twice in file !--}}
        <video id="{{ $getDomId() }}" controls preload="metadata" data-mle-video>
            <source src="{{ $medium->getUrl() }}" type="{{ $medium->mime_type }}">
            {{ __('medialibrary-extensions::messages.your_browser_does_not_support_the_video_element') }}
        </video>
    </div>
</div>
