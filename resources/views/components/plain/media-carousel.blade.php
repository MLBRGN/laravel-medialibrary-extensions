<div {{ $attributes->merge(['class' => 'mlbrgn-mle-component']) }}>
    <div id="{{ $id }}"
         class="media-carousel plain-carousel mle-width-100 mle-height-100"
         data-carousel
         data-carousel-id="{{ $id }}"
         @if(config('media-library-extensions.carousel_ride'))
            data-carousel-ride="{{ config('media-library-extensions.carousel_ride_only_after_interaction') ? 'true' : 'false' }}"
            data-carousel-ride-interval="{{ config('media-library-extensions.carousel_ride_interval') }}"
            data-carousel-ride-only-after-interaction="{{ config('media-library-extensions.carousel_ride_only_after_interaction') ? 'true' : 'false' }}"
         @endif
        >

        {{-- Indicators --}}
        <div
            class="media-carousel-indicators carousel-indicators {{ $mediaItems->count() < 2 ? 'mle-display-none' : '' }}">
            @foreach($mediaItems as $index => $medium)
                <button
                    type="button"
                    data-slide-to="{{ $index }}"
                    class="{{ $loop->first ? 'active' : '' }}"
                    aria-label="{{ __('media-library-extensions::messages.slide_to_:index', ['index' => $index + 1]) }}">
                </button>
            @endforeach
        </div>

        {{-- Slides --}}
        <div class="media-carousel-inner">
            @foreach($mediaItems as $index => $medium)
                <div class="carousel-item {{ $loop->first ? 'active' : '' }} {{ $clickToOpenInModal ? 'mle-cursor-zoom-in' : '' }}">
                    <div class="carousel-item-wrapper d-flex align-items-center justify-content-center"
                         @if($clickToOpenInModal)
                             data-modal-id="{{ $id }}-modal"
                             data-slide-to="{{ $loop->index }}"
                             data-modal-trigger="{{ $id }}-modal"
                        @endif>
                        @if($medium->hasCustomProperty('youtube-id'))
                            <x-mle-video-youtube
                                class="mle-video-responsive"
                                :medium="$medium"
                                :preview="$inModal ? false : true"
                                :youtube-id="$medium->getCustomProperty('youtube-id')"
                                :youtube-params="$inModal ? ['mute' => 1] : []"
                            />
                        @else
                            <x-mle-image-responsive
                                class="mle-image-responsive"
                                :medium="$medium"
                                :conversions="['16x9']"
                                sizes="100vw"
                                :alt="$medium->name"
                            />
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Prev/Next controls --}}
        @if ($mediaItems->count() > 1)
            <button type="button" class="carousel-control-prev" data-slide="prev">
                <span aria-hidden="true">&#10094;</span>
                <span class="mle-visually-hidden">{{ __('media-library-extensions::messages.previous') }}</span>
            </button>
            <button type="button" class="carousel-control-next" data-slide="next">
                <span aria-hidden="true">&#10095;</span>
                <span class="mle-visually-hidden">{{ __('media-library-extensions::messages.next') }}</span>
            </button>
        @endif
    </div>

    @if($clickToOpenInModal)
        <x-mle-media-modal
            :id="$id"
            :model="$model"
            :media-collection="$mediaCollection"
            :media-collections="$mediaCollections"
            title="Media carousel"/>
    @endif
</div>
<x-mle-partial-assets include-css="true" include-js="true" include-youtube-player="{{ config('media-library-extensions.youtube_support_enabled') }}"/>