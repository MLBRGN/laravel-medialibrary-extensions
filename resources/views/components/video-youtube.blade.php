@if ($preview)
    <div class="youtube-video">
        <svg class="youtube-play-button" width="32" height="32">
{{--            <use href="/images/svg-sprites/bootstrap-icons-sprite.svg#play-btn-fill"></use>--}}
        </svg>
        {{ $medium->img()->lazy()->attributes(['class' => 'object-fit-contain']) }}
    </div>
@else
    <div class="media-video-wrapper" data-youtube-video data-youtube-video-id="{{ $youtubeId }}">
        <lite-youtube
            id="yt-video-slide"
            videoid="{{ $youtubeId }}"
            posterquality="maxresdefault"
            autopause
            autoload
            params="autoplay=1&loop=0&controls=0&modestbranding=1&playsinline=1&rel=0&enablejsapi=1"
        >
            <a class="lite-youtube-fallback" href="https://www.youtube.com/watch?v={{ $youtubeId }}">{{ __('media-library-extensions::messages.watch_on_youtube') }}</a>
        </lite-youtube>
    </div>
@endif