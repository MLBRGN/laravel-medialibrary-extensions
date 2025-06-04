@if ($preview)
    <div {{ $attributes->merge() }} class="youtube-video">
        <x-mle-partial-icon
            class="youtube-play-button"
            name="{{ config('media-library-extensions.icons.play_video') }}"
            title="{{ __('media-library-extensions::messages.medium_set_as_main') }}"
        />
{{--        <svg class="youtube-play-button" width="32" height="32">--}}
{{--            <use href="/images/svg-sprites/bootstrap-icons-sprite.svg#play-btn-fill"></use>--}}
{{--        </svg>--}}
        {{ $medium->img()->lazy()->attributes(['class' => 'object-fit-contain']) }}
    </div>
@else
    <div {{ $attributes->merge() }} class="media-video-wrapper" data-youtube-video-id="{{ $youtubeId }}">
        <lite-youtube
            id="yt-video-slide"
            videoid="{{ $youtubeId }}"
            posterquality="maxresdefault"
            autopause
            autoload
            autopause
            params="autoplay=1&loop=0&controls=0&modestbranding=1&playsinline=1&rel=0&enablejsapi=1"
        >
            <a class="lite-youtube-fallback" href="https://www.youtube.com/watch?v={{ $youtubeId }}">{{ __('media-library-extensions::messages.watch_on_youtube') }}</a>
        </lite-youtube>
    </div>
@endif