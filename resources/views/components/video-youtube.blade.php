@if ($preview)
    <div {{ $attributes->merge(['class' => 'youtube-video']) }}>
        <x-mle-partial-icon
            class="youtube-play-button"
            name="{{ config('media-library-extensions.icons.play_video') }}"
            title="{{ __('media-library-extensions::messages.play_video') }}"
        />
        {{ $medium->img()->lazy()->attributes(['class' => 'mle-image-responsive']) }}
    </div>
@else
    <div {{ $attributes->merge(['class' => 'media-video-wrapper']) }} data-youtube-video-id="{{ $youtubeId }}">
        <lite-youtube
            id="yt-video-slide"
            videoid="{{ $youtubeId }}"
            posterquality="maxresdefault"
            autopause
            autoload
            autopause
            params="{{ $youTubeParamsAsString }}"
        >
            <a 
                class="lite-youtube-fallback" 
                href="https://www.youtube.com/watch?v={{ $youtubeId }}">{{ __('media-library-extensions::messages.watch_on_youtube') }}</a>
        </lite-youtube>
    </div>
@endif