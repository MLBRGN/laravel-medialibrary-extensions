{{--
documentation:
-   Used by both manager-multiple.blade and previewer.blade
-   Only works with one YT video at the moment, javascript assumes
    the video has an id of "yt-video-slid"
-   when passed an attribute "autoplay" this attribute is passed on to media-preview-modal and
    will cause any youtube video to start autoplaying,
    playing stops when closing the dialog or sliding to another slide
--}}
@props([
    'modalId' => 'media-preview-modal',
    'model' => null,
    'mediaCollectionName' => null,
    'youtubeCollectionName' => null,
    'logoCollectionName' => null,
    'title' => '',// not shown only for accessibility
    'singleMedium' => false,
])

@php
    // Combine the media items from the collections
    $mediaItems = $model->getMedia($mediaCollectionName);

    // Prepend the enterprise logo if it exists
    if (!is_null($logoCollectionName)) {
        $enterpriseLogo = $model->getMedia($logoCollectionName);
        if ($enterpriseLogo->isNotEmpty()) {
            $mediaItems = $enterpriseLogo->concat($mediaItems); // logo goes first
        }
    }

    // Append YouTube media if it exists
    if (!is_null($youtubeCollectionName)) {
        $mediaItems = $mediaItems->concat($model->getMedia($youtubeCollectionName));
    }
@endphp
<x-info-modal {{ $attributes->merge(['class' => 'media-preview-modal']) }} :modal-id="$modalId" title="{{ $title }}" :show-header="false" :with-padding="false" data-modal-autofocus>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluit"></button>
    <div id="{{$modalId}}-carousel" class="media-preview-modal-carousel carousel slide">
        @if(!$singleMedium)
            <div id="{{$modalId}}-carousel-indicators" class="carousel-indicators">
                @foreach($mediaItems as $index => $medium)
                    <button type="button" data-bs-target="#{{$modalId}}-carousel" data-bs-slide-to="{{ $loop->index }}"
                            class="{{ $loop->first ? 'active' : '' }}" {!! $loop->first ? 'aria-current="true"' : '' !!} aria-label="Afbeelding {{ $medium->name }}">
                    </button>
                @endforeach
            </div>
        @endif
        <div class="carousel-inner" data-bs-toggle="modal">
            @foreach($mediaItems as $index => $medium)
                @if($medium->hasCustomProperty('youtube-id'))
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}" data-youtube-video data-youtube-video-id="{{ $medium->getCustomProperty('youtube-id') }}">
                        <div class="carousel-item-wrapper d-flex align-items-center justify-content-center">
                            <div class="video-wrapper">
                                <lite-youtube
                                    id="yt-video-slide"
                                    videoid="{{ $medium->getCustomProperty('youtube-id') }}"
                                    posterquality="maxresdefault"
                                    autopause
                                    autoload
                                    params="autoplay=1&loop=0&controls=0&modestbranding=1&playsinline=1&rel=0&enablejsapi=1"
                                    >
                                    <a class="lite-youtube-fallback" href="https://www.youtube.com/watch?v={{ $medium->getAttribute('youtube-id') }}">Bekijk op YouTube</a>
                                </lite-youtube>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                        <div class="carousel-item-wrapper d-flex align-items-center justify-content-center">
                            <x-media-library-extensions::library-image class="image image-zoomed" :media="$medium" conversion="16x9" sizes="95vw" />
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        @if(!$singleMedium)
            <button
                @class([
                    'carousel-control-prev',
                    'disabled' => count($mediaItems) <= 1
                    ]) type="button" data-bs-target="#{{$modalId}}-carousel"
                    data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Vorige</span>
            </button>
            <button @class([
                    'carousel-control-next',
                    'disabled' => count($mediaItems) <= 1
                    ]) type="button" data-bs-target="#{{$modalId}}-carousel"
                    data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Volgende</span>
            </button>
        @endif
    </div>
</x-info-modal>
{{--<script src="https://www.youtube.com/iframe_api"></script>--}}
{{--@once--}}
{{--    @vite('resources/js/modules/mediaPreviewModal.js')--}}
{{--@endonce--}}
