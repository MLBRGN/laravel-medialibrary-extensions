<div id="{{ $id }}"
     {{ $attributes->class([
        'mlbrgn-mle-component',
        'theme-'.$frontendTheme,
        'media-carousel', 
        'media-carousel-empty' => $mediaCount === 0,
        'media-carousel-plain',
        'mle-width-100',
        'mle-height-100'   
    ])->merge() }}
     data-carousel
     data-carousel-id="{{ $id }}"
     tabindex="-1"
     @if(config('media-library-extensions.carousel_ride'))
        data-carousel-ride="{{ config('media-library-extensions.carousel_ride') ? 'true' : 'false' }}"
        data-carousel-ride-interval="{{ config('media-library-extensions.carousel_ride_interval') }}"
        data-carousel-ride-only-after-interaction="{{ config('media-library-extensions.carousel_ride_only_after_interaction') ? 'true' : 'false' }}"
     @endif
     data-carousel-effect="{{ config('media-library-extensions.carousel_fade') ? 'fade' : 'slide' }}"
    >

    {{-- Indicators --}}
    <div
        @class([
            'media-carousel-indicators', 
            'carousel-indicators', 
            'mle-display-none' => $mediaCount < 2
        ])
    >
        @foreach($mediaItems as $index => $medium)
            <button
                type="button"
                data-slide-to="{{ $index }}"
                @class(['active' => $loop->first])
                @if($loop->first) 
                    aria-current="true" 
                @endif
                aria-label="{{ __('media-library-extensions::messages.slide_to_:index', ['index' => $index + 1]) }}">
            </button>
        @endforeach
    </div>

    {{-- Slides --}}
    <div class="media-carousel-inner">
        @forelse($mediaItems as $index => $medium)
            <div @class([
                'media-carousel-item',
                'active' => $loop->first,
                'mle-cursor-zoom-in' => $clickToOpenInModal
            ])>
                <div class="media-carousel-item-container"
                     @if($clickToOpenInModal)
                         data-modal-trigger="{{ $id }}-modal"
                         data-slide-to="{{ $loop->index }}"
                    @endif
                >
                    @if(isMediaType($medium, 'youtube-video'))
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
                            />
                        @endif
                    @elseif(isMediaType($medium, 'document'))
                        <x-mle-document :medium="$medium"
                            class="mle-document mle-cursor-zoom-in"
                        />
                    @elseif(isMediaType($medium, 'video'))
                        <div
                            data-bs-toggle="modal"
                            data-bs-target="#{{$id}}-modal"
                            class="media-manager-preview-item-container"
                        >
                            <x-mle-video :medium="$medium" />
                        </div>
                    @elseif(isMediaType($medium, 'audio'))
                        <div
                            data-bs-toggle="modal"
                            data-bs-target="#{{$id}}-modal"
                            class="media-manager-preview-item-container"
                        >
                            <x-mle-audio :medium="$medium" />
                        </div>
                    @elseif(isMediaType($medium, 'image'))
                        <img
                            src="{{ $medium->getUrl() }}"
                            class="media-manager-image-preview mle-cursor-zoom-in"
                            alt="{{ $medium->name }}"
                        />
                    @endif
                </div>
            </div>
        @empty
            <div @class([
                'media-carousel-item',
                'active',
            ])>
                <div class="media-carousel-item-container">
                    <span class="mle-no-media">{{ __('media-library-extensions::messages.no_media') }}</span>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Prev/Next controls --}}
    <button 
        @class([
          'media-carousel-control-prev',
          'disabled' => $mediaCount <= 1
         ])
        type="button"
        data-slide="prev">
        <span class="media-carousel-control-prev-icon" aria-hidden="true">
             <x-mle-partial-icon
                 name="{{ config('media-library-extensions.icons.prev') }}"
                 title="{{ __('media-library-extensions::messages.previous') }}"
             />
        </span>
        <span class="mle-visually-hidden">{{ __('media-library-extensions::messages.previous') }}</span>
    </button>
    <button 
        @class([
         'media-carousel-control-next',
         'disabled' => $mediaCount <= 1
        ])
        type="button"
        data-slide="next">
        <span class="media-carousel-control-next-icon" aria-hidden="true">
              <x-mle-partial-icon
                  name="{{ config('media-library-extensions.icons.next') }}"
                  title="{{ __('media-library-extensions::messages.next') }}"
              />
        </span>
        <span class="mle-visually-hidden">{{ __('media-library-extensions::messages.next') }}</span>
    </button>
</div>

@if($clickToOpenInModal)
    <x-mle-media-modal
        :id="$id"
        :model-or-class-name="$modelOrClassName"
        :media-collection="$mediaCollection"
        :media-collections="$mediaCollections"
        title="Media carousel"/>
@endif
<x-mle-partial-assets include-css="true" include-js="true" include-youtube-player="{{ config('media-library-extensions.youtube_support_enabled') }}" :frontend-theme="$frontendTheme"/>
