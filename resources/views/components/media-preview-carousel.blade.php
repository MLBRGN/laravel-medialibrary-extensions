<div
    id="{{ $id }}-carousel"
    {{ $attributes->merge([
        'class' => mle_media_class('media-manager-preview-modal-carousel')
     ]) }}>
    @if(!$singleMedium)
        <div
            id="{{ $id }}-carousel-indicators"
            class="@mediaClass('media-manager-preview-modal-carousel-indicators')">
            @foreach($mediaItems as $index => $medium)
                <button
                    type="button"
                    data-bs-target="#{{$id}}-carousel"
                    data-bs-slide-to="{{ $loop->index }}"
                    {!! $loop->first ? 'aria-current="true"' : '' !!}
                    aria-label="Afbeelding {{ $medium->name }}"
                    @class([
                        'active' => $loop->first
                    ])>
                </button>
            @endforeach
        </div>
    @endif
    <div
        class="@mediaClass('media-manager-preview-modal-carousel-inner')"
        data-bs-toggle="modal">
        @foreach($mediaItems as $index => $medium)
            {{--                TODO--}}
            {{--                @if($medium->hasCustomProperty('youtube-id'))--}}
            {{--                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}" data-youtube-video data-youtube-video-id="{{ $medium->getCustomProperty('youtube-id') }}">--}}
            {{--                        <div class="carousel-item-wrapper d-flex align-items-center justify-content-center">--}}
            {{--                            <div class="video-wrapper">--}}
            {{--                                <lite-youtube--}}
            {{--                                    id="yt-video-slide"--}}
            {{--                                    videoid="{{ $medium->getCustomProperty('youtube-id') }}"--}}
            {{--                                    posterquality="maxresdefault"--}}
            {{--                                    autopause--}}
            {{--                                    autoload--}}
            {{--                                    params="autoplay=1&loop=0&controls=0&modestbranding=1&playsinline=1&rel=0&enablejsapi=1"--}}
            {{--                                    >--}}
            {{--                                    <a class="lite-youtube-fallback" href="https://www.youtube.com/watch?v={{ $medium->getAttribute('youtube-id') }}">Bekijk op YouTube</a>--}}
            {{--                                </lite-youtube>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                @else--}}
            <div
                @class([
                    mle_media_class('media-manager-preview-modal-carousel-item'),
                    'active' => $loop->first
                ])
            >
                <div class="@mediaClass('media-manager-preview-modal-carousel-item-wrapper')">
                    <x-mle-image-responsive :medium="$medium" />
{{--                    {{ $medium->img()->lazy()->attributes([--}}
{{--                        'class' => mle_media_class('media-manager-preview-modal-carouse-item-image')--}}
{{--                        ]) }}--}}
                    {{--                                TODO--}}
                    {{--                            <x-media-library-extensions::library-image class="image image-zoomed" :media="$medium" conversion="16x9" sizes="95vw" />--}}
                </div>
            </div>
            {{--                @endif--}}
        @endforeach
    </div>
    @if(!$singleMedium)
        <button
            @class([
                mle_media_class('media-manager-preview-modal-carousel-control-prev'),
                'disabled' => count($mediaItems) <= 1
                ])
            type="button"
            data-bs-target="#{{$id}}-carousel"
            data-bs-slide="prev">
                <span
                    class="@mediaClass('media-manager-preview-modal-carousel-control-prev-icon')carousel-control-prev-icon"
                    aria-hidden="true"></span>
            <span class="@mediaClass('visually-hidden')">{{ __('media-library-extensions::messages.previous') }}</span>
        </button>
        <button @class([
                    mle_media_class('media-manager-preview-modal-carousel-control-next'),
                    'disabled' => count($mediaItems) <= 1
                    ])
                type="button"
                data-bs-target="#{{ $id }}-carousel"
                data-bs-slide="next">
                <span class=@mediaClass('media-manager-preview-modal-carousel-control-next-icon')"
                      aria-hidden="true"></span>
            <span class="@mediaClass('visually-hidden')">Volgende</span>
        </button>
    @endif
</div>
