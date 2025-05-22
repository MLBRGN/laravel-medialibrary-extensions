<x-mle_internal-modal {{ $attributes->merge([
                'class' => mle_media_class('media-manager-preview-modal')
             ]) }}
             :modal-id="$modalId"
             title="{{ $title }}"
             :show-header="false"
             :no-padding="true"
             :size-class="$sizeClass"
             data-modal-autofocus>
    <button
        type="button"
        class="@mediaClass('button-close')"
        data-bs-dismiss="modal"
        aria-label="Sluit"></button>
    <div
        id="{{$modalId}}-carousel"
        class="@mediaClass('media-manager-preview-modal-carousel')">
        @if(!$singleMedium)
            <div
                id="{{$modalId}}-carousel-indicators"
                class="@mediaClass('media-manager-preview-modal-carousel-indicators')">
                @foreach($mediaItems as $index => $medium)
                    <button
                        type="button"
                        data-bs-target="#{{$modalId}}-carousel"
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
                            {{ $medium->img()->lazy()->attributes([
                                'class' => mle_media_class('media-manager-preview-modal-carouse-item-image')
                                ]) }}
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
                    data-bs-target="#{{$modalId}}-carousel"
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
                    data-bs-target="#{{$modalId}}-carousel"
                    data-bs-slide="next">
                <span class=@mediaClass('media-manager-preview-modal-carousel-control-next-icon')"
                      aria-hidden="true"></span>
                <span class="@mediaClass('visually-hidden')">Volgende</span>
            </button>
        @endif
    </div>
</x-mle_internal-modal>
{{--TODO--}}
{{--<script src="https://www.youtube.com/iframe_api"></script>--}}
{{--@once--}}
{{--    @vite('resources/js/modules/mediaPreviewModal.js')--}}
{{--@endonce--}}
{{--<script>--}}
{{--    @php--}}
{{--        $jsFilePath = public_path('js/vendor/media-library-extensions/mediaPreviewModal.js');--}}
{{--    @endphp--}}
{{--    @if (file_exists($jsFilePath))--}}
{{--        {!! file_get_contents($jsFilePath) !!}--}}
{{--        console.log('found published js')--}}
{{--    @else--}}
{{--        console.log("JavaScript file not found, using inline fallback.");--}}
{{--        // Your fallback JS code here--}}
{{--        // alert("Fallback JS loaded");--}}
{{--    @endif--}}
{{--    {!! file_get_contents(__DIR__ . '/../../js/mediaPreviewModal.js') !!}--}}
{{--</script>--}}
{{--<script src="{{ asset('mlbrgn/spatie-media-library-extensions/mediaPreviewModal.js') }}"></script>--}}
@once
    <link rel="stylesheet" href="{{ mle_package_asset('media-library-extensions.css') }}">

{{--{{ \Illuminate\Support\Facades\Vite::useHotFile('vendor/media-library-extensions/media-library-extensions.hot')--}}
{{--    ->useBuildDirectory("vendor/media-library-extensions")--}}
{{--    ->withEntryPoints(['resources/css/app.scss', 'resources/js/app.js']) }}--}}

{{--<link href="{{ asset('media-library-extensions/css/app.css') }}" rel="stylesheet" />--}}

@endonce
