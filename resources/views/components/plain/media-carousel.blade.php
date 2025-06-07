{{--Bootstrap classes found:--}}
{{--active (common in Bootstrap carousels)--}}
{{--d-flex (Bootstrap flex utility)--}}
{{--align-items-center (Bootstrap flex utility)--}}
{{--justify-content-center (Bootstrap flex utility)--}}

<div {{ $attributes->merge(['class' => 'mlbrgn-mle-component']) }}>
    <div id="{{ $id }}"
         @class([
           'media-carousel', 
           'media-carousel-plain',
           'mle-width-100',
           'mle-height-100'   
        ])
         data-carousel
         data-carousel-id="{{ $id }}"
         tabindex="0"
         @if(config('media-library-extensions.carousel_ride'))
            data-carousel-ride="{{ config('media-library-extensions.carousel_ride') ? 'true' : 'false' }}"
            data-carousel-ride-interval="{{ config('media-library-extensions.carousel_ride_interval') }}"
            data-carousel-ride-only-after-interaction="{{ config('media-library-extensions.carousel_ride_only_after_interaction') ? 'true' : 'false' }}"
         @endif
         data-carousel-effect="{{ config('media-library-extensions.carousel_fade') ? 'fade' : 'slide' }}"
        >

        {{-- Indicators --}}
        <div
            class="media-carousel-indicators {{ $mediaItems->count() < 2 ? 'mle-display-none' : '' }}">
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
                <div @class([
                    'media-carousel-item',
                    'active' => $loop->first,
                    'mle-cursor-zoom-in' => $clickToOpenInModal
                ])>
                    <div class="media-carousel-item-wrapper"
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
                                :youtube-params="[]"
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
            <button 
                type="button"
                @class([
                  'media-carousel-control-prev',
                  'disabled' => count($mediaItems) <= 1
                 ])
                data-slide="prev">
{{--                <span aria-hidden="true">&#10094;</span>--}}
                <span class="media-carousel-control-prev-icon" aria-hidden="true">
                     <x-mle-partial-icon
                         name="{{ config('media-library-extensions.icons.prev') }}"
                         title="{{ __('media-library-extensions::messages.previous') }}"
                     />
                </span>
                <span class="mle-visually-hidden">{{ __('media-library-extensions::messages.previous') }}</span>
            </button>
            <button 
                type="button"
                @class([
                 'media-carousel-control-next',
                 'disabled' => count($mediaItems) <= 1
                ])
                data-slide="next">
{{--                <span aria-hidden="true">&#10095;</span>--}}
                <span class="media-carousel-control-next-icon" aria-hidden="true">
                      <x-mle-partial-icon
                          name="{{ config('media-library-extensions.icons.next') }}"
                          title="{{ __('media-library-extensions::messages.next') }}"
                      />
                </span>
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