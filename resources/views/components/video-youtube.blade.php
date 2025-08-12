@php use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload; @endphp
@if ($preview)
    <div {{ $attributes->merge(['class' => 'mle-youtube-video mle-video-responsive']) }}>
        <x-mle-partial-icon
            class="mle-icon-container-youtube-play-button"
            name="{{ config('media-library-extensions.icons.play_video') }}"
            title="{{ __('media-library-extensions::messages.play_video') }}"
        />
        @if($medium instanceof TemporaryUpload)
            <img
                src="{{ $medium->getUrl() }}"
                class="mle-youtube-video mle-video-responsive mle-cursor-zoom-in"
                alt="{{ $medium->name }}"
            />
        @else
            {{ $medium->img()->lazy()->attributes(['class' => 'mle-image-responsive']) }}
        @endif
            
    </div>
@else
    <div {{ $attributes->merge(['class' => 'media-video-container']) }} data-youtube-video-id="{{ $youtubeId }}">
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
        <div class="media-video-touch-overlay"></div>
    </div>
@endif