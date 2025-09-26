<div {{ $attributes->class('mle-video') }}>
    <div class="mle-video-preview">
        <div class="mle-video-info">
            <p>
                {{ Str::limit($medium->file_name, 15) }}
            </p>
        </div>
        <video id="{{ $id }}" controls preload="metadata" data-mle-video>
            <source src="{{ $medium->getUrl() }}" type="{{ $medium->mime_type }}">
            {{ __('media-library-extensions::messages.your_browser_does_not_support_the_video_element') }}
        </video>
    </div>
</div>
