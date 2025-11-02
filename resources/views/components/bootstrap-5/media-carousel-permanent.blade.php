<div id="{{ $id }}"
     {{ $attributes->class([
        'mle-component',
        'mle-theme-'.$getConfig('frontendTheme'),
        'media-carousel', 
        'mle-media-carousel-empty' => $mediaCount === 0,
        'carousel', 
        'slide',
        'carousel-fade' => config('media-library-extensions.carousel_fade'),
        'mle-width-100',
        'mle-height-100'
      ])->merge() }}
    @if(config('media-library-extensions.carousel_ride'))
        data-bs-ride="{{ config('media-library-extensions.carousel_ride_only_after_interaction') ? 'true' : 'carousel' }}"
        data-bs-interval="{{ config('media-library-extensions.carousel_ride_interval') }}"
    @endif
     data-mle-carousel
>
    {{-- Indicators --}}
    <div 
        @class([
            'mle-media-carousel-indicators', 
            'carousel-indicators', 
            'mle-display-none' => $mediaCount < 2
        ])
        data-mle-carousel-indicators
    >
        @foreach($media as $index => $medium)
            <button
                type="button"
                data-bs-target="#{{ $id }}"
                data-bs-slide-to="{{ $index }}"
                @class(['active' => $loop->first])
                @if($loop->first) 
                    aria-current="true" 
                @endif
                aria-label="{{ __('media-library-extensions::messages.slide_to_:index', ['index' => $index + 1]) }}">
            </button>
        @endforeach
    </div>

    {{-- Slides --}}
    <div class="mle-media-carousel-inner carousel-inner">
        @forelse($media as $index => $medium)
            <div 
                @class([
                'mle-media-carousel-item',
                'carousel-item',
                'active' => $loop->first,
                'mle-cursor-zoom-in' => $expandableInModal,
            ])
            data-mle-carousel-item
            >
                <div class="mle-media-carousel-item-container" 
                     data-bs-toggle="modal"
                     data-bs-target="#{{$id}}-mod"
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
                            data-bs-target="#{{$id}}-mod-crs"
                            data-bs-slide-to="{{ $loop->index }}"
                        />
                    @endif
                @elseif(isMediaType($medium, 'document'))
                    <x-mle-document :medium="$medium"
                                    class="mle-document mle-cursor-zoom-in"
                                    data-bs-target="{{ $id }}-mod"
                                    data-bs-slide-to="{{ $loop->index }}"
                                    :preview="false"
                    />
                @elseif(isMediaType($medium, 'video'))
                    <div
                        data-bs-toggle="modal"
                        data-bs-target="#{{$id}}-mod"
                        class="mle-media-preview-item-container"
                    >
                        <x-mle-video :medium="$medium" />
                    </div>
                @elseif(isMediaType($medium, 'audio'))
                    <div
                        data-bs-toggle="modal"
                        data-bs-target="#{{$id}}-mod"
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
                        data-bs-target="#{{$id}}-mod-crs"
                        data-bs-slide-to="{{ $loop->index }}"
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
             'carousel-control-prev',
             'disabled' => $mediaCount <= 1
         ])
        type="button" 
        data-bs-target="#{{ $id }}" 
        data-bs-slide="prev" 
        title="{{ __('media-library-extensions::messages.previous') }}">
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
            'carousel-control-next',
            'disabled' => $mediaCount <= 1
        ])
        type="button" 
        data-bs-target="#{{ $id }}" 
        data-bs-slide="next" 
        title="{{ __('media-library-extensions::messages.next') }}">
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
    include-carousel-js="true"
    include-lite-youtube="{{ config('media-library-extensions.youtube_support_enabled') }}" 
    :frontend-theme="$getConfig('frontendTheme')"
/>
