<div {{ $attributes->merge(['class' => 'mlbrgn-mle-component']) }}>
    <div id="{{ $id }}"
         @class([
             'media-carousel', 
             'carousel', 
             'slide',
             'carousel-fade' => config('media-library-extensions.carousel_fade'),
             'mle-width-100',
             'mle-height-100'   
          ])
        @if(config('media-library-extensions.carousel_ride'))
            data-bs-ride="{{ config('media-library-extensions.carousel_ride_only_after_interaction') ? 'true' : 'carousel' }}"
            data-bs-interval="{{ config('media-library-extensions.carousel_ride_interval') }}"
        @endif
    >
        {{-- Indicators --}}
        <div 
            @class([
                'media-carousel-indicators', 
                'carousel-indicators', 
                'mle-display-none' => $mediaItems->count() < 2
            ])>
            @foreach($mediaItems as $index => $medium)
                <button
                    type="button"
                    data-bs-target="#{{ $id }}"
                    data-bs-slide-to="{{ $index }}"
                    @class(['active' => $loop->first])
                    @if($loop->first) aria-current="true" @endif
                    aria-label="{{ __('media-library-extensions::messages.slide_to_:index', ['index' => $index + 1]) }}">
                </button>
            @endforeach
        </div>

        {{-- Slides --}}
        <div class="media-carousel-inner carousel-inner">
            @foreach($mediaItems as $index => $medium)

                <div @class([
                    'media-carousel-item',
                    'carousel-item',
                    'active' => $loop->first,
                    'mle-cursor-zoom-in' => $clickToOpenInModal
                ])>
                    <div class="media-carousel-item-wrapper" 
                         data-bs-toggle="modal"
                         data-bs-target="#{{$id}}-modal"
                    >
                    @if($medium->hasCustomProperty('youtube-id'))
                        @if ($inModal)
                            <x-mle-video-youtube
                                class="mle-video-responsive"
                                :medium="$medium" 
                                :preview="false" 
                                :youtube-id="$medium->getCustomProperty('youtube-id')"
                                :youtube-params="[]"
                            />
                        @else
                            <x-mle-video-youtube
                                class="mle-video-responsive"
                                :medium="$medium" 
                                :preview="true"  
                                data-bs-target="#{{$id}}-modal-carousel"
                                data-bs-slide-to="{{ $loop->index }}"
                            />
                        @endif
                    @else
                        <x-mle-image-responsive
                            class="mle-image-responsive"
                            :medium="$medium"
                            :conversions="['16x9']"
                            sizes="100vw"
                            :alt="$medium->name"
                            data-bs-target="#{{$id}}-modal-carousel"
                            data-bs-slide-to="{{ $loop->index }}"
                        />
                    @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Prev/Next controls --}}
        <button
            @class([
                 'media-carousel-control-prev',
                 'carousel-control-prev',
                 'disabled' => count($mediaItems) <= 1
             ])
            type="button" 
            data-bs-target="#{{ $id }}" 
            data-bs-slide="prev" 
            title="{{ __('media-library-extensions::messages.previous') }}">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">{{ __('media-library-extensions::messages.previous') }}</span>
        </button>
        <button
            @class([
                'media-carousel-control-next',
                'carousel-control-next',
                'disabled' => count($mediaItems) <= 1
            ])
            type="button" 
            data-bs-target="#{{ $id }}" 
            data-bs-slide="next" 
            title="{{ __('media-library-extensions::messages.next') }}">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">{{ __('media-library-extensions::messages.next') }}</span>
        </button>
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
