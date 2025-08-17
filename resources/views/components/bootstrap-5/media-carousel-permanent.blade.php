<div id="{{ $id }}"
     {{ $attributes->class([
        'mlbrgn-mle-component',
        'theme-'.$frontendTheme,
        'media-carousel', 
        'media-carousel-empty' => $mediaCount === 0,
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
    <div class="media-carousel-inner carousel-inner">
        @forelse($mediaItems as $index => $medium)
            <div 
{{--                id="{{ $id }}-slide-{{ $loop->index }}" --}}
                @class([
                'media-carousel-item',
                'carousel-item',
                'active' => $loop->first,
                'mle-cursor-zoom-in' => $clickToOpenInModal,
            ])>
                <div class="media-carousel-item-container" 
                     data-bs-toggle="modal"
                     data-bs-target="#{{$id}}-modal"
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
                            data-bs-target="#{{$id}}-modal-carousel"
                            data-bs-slide-to="{{ $loop->index }}"
                        />
                    @endif
                @elseif(isMediaType($medium, 'document'))
                    <x-mle-document :medium="$medium"
                                    class="mle-document mle-cursor-zoom-in"
                                    data-bs-target="{{ $id }}-modal"
                                    data-bs-slide-to="{{ $loop->index }}"
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
                    <x-mle-image-responsive
                        class="mle-image-responsive"
                        :medium="$medium"
                        :conversions="['16x9']"
                        sizes="100vw"
                        :alt="$medium->name"
                        data-bs-target="#{{$id}}-modal-carousel"
                        data-bs-slide-to="{{ $loop->index }}"
                        draggable="false"
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
             'carousel-control-prev',
             'disabled' => $mediaCount <= 1
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
            'disabled' => $mediaCount <= 1
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
        :model-or-class-name="$modelOrClassName"
        :media-collection="$mediaCollection"
        :media-collections="$mediaCollections"
        title="Media carousel"/>
@endif
<x-mle-partial-assets include-css="true" include-js="true" include-youtube-player="{{ config('media-library-extensions.youtube_support_enabled') }}" :frontend-theme="$frontendTheme"/>
