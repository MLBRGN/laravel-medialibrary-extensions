@php use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload; @endphp
@if ($preview)
    <div class="mlbrgn-mle-component youtube-container">
        <div {{ $attributes->merge(['class' => 'mle-youtube-video mle-video-responsive']) }}>
            <x-mle-shared-icon
                class="mle-icon-container-youtube-play-button"
                name="{{ config('media-library-extensions.icons.play_video') }}"
                title="{{ __('media-library-extensions::messages.play_video') }}"
            />
            @if($medium instanceof TemporaryUpload)
                <img
                    src="{{ $medium->getUrl() }}"
                    class="mle-youtube-video mle-video-responsive mle-cursor-zoom-in"
                    alt="{{ $medium->name }}"
                    draggable="false"
                >
            @else
                {{ 
                    $medium->img()
                    ->lazy()
                    ->attributes(['class' => 'mle-image-responsive']) 
                }}
            @endif
        </div>
    </div>
@else
    <div class="mlbrgn-mle-component">
        <div {{ $attributes->merge(['class' => 'mle-component media-video-container']) }} data-youtube-video-id="{{ $youtubeId }}">
            <lite-youtube
{{--                id="yt-video-slide"--}}
                videoid="{{ $youtubeId }}"
                posterquality="maxresdefault"
                autopause
                autoload
                params="{{ $youTubeParamsAsString }}"
                tabindex="-1"
            >
                <a
                    class="lite-youtube-fallback"
                    href="https://www.youtube.com/watch?v={{ $youtubeId }}"
                    target="_blank"
                    tabindex="-1"
                >
                    <div class="mle-youtube-video mle-video-responsive">
                        <x-mle-shared-icon
                            class="mle-icon-container-youtube-play-button"
                            name="{{ config('media-library-extensions.icons.play_video') }}"
                            title="{{ __('media-library-extensions::messages.play_video') }}"
                        >
                            
                        </x-mle-partial-icon>
                        <div class="mle-youtube-video-fallback-text">
                            <p>
                                {{ __('media-library-extensions::messages.could_not_load_video') }}
                                {{ __('media-library-extensions::messages.watch_on_youtube') }}
                            </p>
                        </div>
                    </div>
                </a>
            </lite-youtube>
            <div class="media-video-touch-overlay"></div>
        </div>
    </div>
@endif
<x-mle-shared-assets include-css="true" include-js="false" include-youtube-player="true" :frontend-theme="$frontendTheme"/>
