<div id="{{ $id }}"
     {{ $attributes->class([
        'mle-component',
        'theme-'.$getConfig('frontendTheme'),
        'mle-media-carousel', 
        'mle-media-carousel-empty' => $mediaCount === 0,
        'mle-media-carousel-plain',
        'mle-width-100',
        'mle-height-100'   
    ])->merge() }}
     data-mle-carousel
     data-mle-carousel-id="{{ $id }}"
     tabindex="-1"
     @if(config('media-library-extensions.carousel_ride'))
        data-mle-carousel-ride="{{ config('media-library-extensions.carousel_ride') ? 'true' : 'false' }}"
        data-mle-carousel-ride-interval="{{ config('media-library-extensions.carousel_ride_interval') }}"
        data-mle-carousel-ride-only-after-interaction="{{ config('media-library-extensions.carousel_ride_only_after_interaction') ? 'true' : 'false' }}"
     @endif
     data-mle-carousel-effect="{{ config('media-library-extensions.carousel_fade') ? 'fade' : 'slide' }}"
    >

    {{-- Indicators --}}
    <div
        @class([
            'mle-media-carousel-indicators', 
//            'carousel-indicators', 
            'mle-display-none' => $mediaCount < 2
        ])
        data-mle-carousel-indicators
    >
        @foreach($media as $index => $medium)
            <button
                type="button"
                data-mle-slide-to="{{ $index }}"
                @class(['active' => $loop->first])
                @if($loop->first) 
                    aria-current="true" 
                @endif
                aria-label="{{ __('media-library-extensions::messages.slide_to_:index', ['index' => $index + 1]) }}">
            </button>
        @endforeach
    </div>

    {{-- Slides --}}
    <div class="mle-media-carousel-inner">
        @forelse($media as $index => $medium)
            <div
{{--                id="{{ $id }}-slide-{{ $loop->index }}"--}}
                @class([
                    'mle-media-carousel-item',
                    'active' => $loop->first,
                    'mle-cursor-zoom-in' => $expandableInModal
                ])
                data-mle-carousel-item
            >
                <div class="mle-media-carousel-item-container"
                     @if($expandableInModal)
{{--                         data-mle-modal-trigger="#{{ $id }}-modal"--}}
                         data-mle-modal-trigger="#{{ $id }}-mod"
                         data-mle-slide-to="{{ $loop->index }}"
                    @endif
                >
                    @if(isMediaType($medium, 'youtube-video'))
                        @if ($inModal)
                            <x-mle-video-youtube
                                class="mle-video-responsive"
                                :medium="$medium"
                                :preview="false"
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
                            :preview="false"
                        />
                    @elseif(isMediaType($medium, 'video'))
                        <div
{{--                            data-mle-modal-trigger="#{{ $id }}-modal"--}}
                            data-mle-modal-trigger="#{{ $id }}-mod"
                            class="mle-media-preview-item-container"
                        >
                            <x-mle-video :medium="$medium" />
                        </div>
                    @elseif(isMediaType($medium, 'audio'))
                        <div
{{--                            data-mle-modal-trigger="#{{ $id }}-modal"--}}
                            data-mle-modal-trigger="#{{ $id }}-mod"
                            class="mle-media-preview-item-container"
                        >
                            <x-mle-audio :medium="$medium" />
                        </div>
                    @elseif(isMediaType($medium, 'image'))
                        <x-mle-image-responsive
                            class="mle-image-responsive"
                            :medium="$medium"
                            :conversions="['16x9']"
                            sizes="100vw"
                            :alt="$medium->name"
                            draggable="false"
                        />
                    @endif
                </div>
            </div>
        @empty
            <div @class([
                    'mle-media-carousel-item',
                    'active',
                ])
                data-mle-carousel-item
            >
                <div class="mle-media-carousel-item-container">
                    <span class="mle-no-media">{{ __('media-library-extensions::messages.no_media') }}</span>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Prev/Next controls --}}
    <button 
        @class([
          'mle-media-carousel-control-prev',
          'disabled' => $mediaCount <= 1
         ])
        type="button"
        data-slide="prev">
        <span class="mle-media-carousel-control-prev-icon" aria-hidden="true">
             <x-mle-shared-icon
                 name="{{ config('media-library-extensions.icons.prev') }}"
                 title="{{ __('media-library-extensions::messages.previous') }}"
             />
        </span>
        <span class="mle-visually-hidden">{{ __('media-library-extensions::messages.previous') }}</span>
    </button>
    <button 
        @class([
         'mle-media-carousel-control-next',
         'disabled' => $mediaCount <= 1
        ])
        type="button"
        data-slide="next">
        <span class="mle-media-carousel-control-next-icon" aria-hidden="true">
              <x-mle-shared-icon
                  name="{{ config('media-library-extensions.icons.next') }}"
                  title="{{ __('media-library-extensions::messages.next') }}"
              />
        </span>
        <span class="mle-visually-hidden">{{ __('media-library-extensions::messages.next') }}</span>
    </button>
</div>

@if($expandableInModal)
    <x-mle-media-modal
        :id="$id"
        :model-or-class-name="$modelOrClassName"
        :single-medium="$singleMedium"
        :media-collection="$mediaCollection"
        :media-collections="$mediaCollections"
        :options="$options"
        title="Media carousel"/>
@endif
<x-mle-shared-assets 
    include-css="true" 
    include-js="true" 
    include-lite-youtube="{{ config('media-library-extensions.youtube_support_enabled') }}" 
    include-carousel-js="true"
    :frontend-theme="$getConfig('frontendTheme')"
/>
