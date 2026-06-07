<div id="{{ $id }}"
     {{ $attributes->class([
        'mle-component',
        'mle-theme-'.$getConfig('frontendTheme'),
        'mle-media-carousel',
        'media-carousel', 
        'mle-media-carousel-empty' => $mediaCount === 0,
        'carousel', 
        'slide',
        'carousel-fade' => config('medialibrary-extensions.carousel_fade'),
        'mle-width-100',
        'mle-height-100'
    ])->merge() }}
    @if(config('medialibrary-extensions.carousel_ride'))
        data-bs-ride="{{ config('medialibrary-extensions.carousel_ride_only_after_interaction') ? 'true' : 'carousel' }}"
        data-bs-interval="{{ config('medialibrary-extensions.carousel_ride_interval') }}"
    @endif
    data-mle-carousel
>
{{--    @if(config('medialibrary-extensions.debug'))--}}
{{--        <div class="mle-component mle-debug-menu">--}}
{{--            <x-mle-shared-debug-button/>--}}
{{--            <x-mle-shared-local-package-icon />--}}
{{--        </div>--}}
{{--    @endif--}}

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
                aria-label="{{ __('medialibrary-extensions::messages.slide_to_:index', ['index' => $index + 1]) }}">
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
                    'mle-cursor-zoom-in' => $expandableInModal
                ])
                data-mle-carousel-item
            >
                <div class="mle-media-carousel-item-container"
                     @if($expandableInModal)
                         data-bs-toggle="modal"
                         data-bs-target="#{{$id}}-mod"
                    @endif
                >
                    <x-mle-media-viewer
                        :id="$id . '-' . $loop->index"
                        :medium="$medium"
                        :options="$getOptions()"
                        :preview-mode="$previewMode"
                        :expandable-in-modal="$expandableInModal"
                        data-bs-target="#{{ $id }}-mod-crs"
                        data-bs-slide-to="{{ $loop->index }}"
                    />
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
                    <span class="mle-no-media">{{ __('medialibrary-extensions::messages.no_media') }}</span>
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
        title="{{ __('medialibrary-extensions::messages.previous') }}"
    >
        <span class="mle-media-carousel-control-prev-icon" aria-hidden="true">
        <x-mle-shared-icon
            name="{{ config('medialibrary-extensions.icons.prev') }}"
            title="{{ __('medialibrary-extensions::messages.previous') }}"
        />
        </span>
        <span class="mle-visually-hidden">{{ __('medialibrary-extensions::messages.previous') }}</span>
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
        title="{{ __('medialibrary-extensions::messages.next') }}"
    >
        <span class="mle-media-carousel-control-next-icon" aria-hidden="true">
            <x-mle-shared-icon
                name="{{ config('medialibrary-extensions.icons.next') }}"
                title="{{ __('medialibrary-extensions::messages.next') }}"
            />
        </span>
        <span class="mle-visually-hidden">{{ __('medialibrary-extensions::messages.next') }}</span>
    </button>
{{--    <x-mle-shared-debug--}}
{{--        :model-or-class-name="$modelOrClassName"--}}
{{--        :config="$getConfig()"--}}
{{--        :options="$getOptions()"--}}
{{--    />--}}
</div>
@if($expandableInModal)
    <x-mle-media-modal
        :id="$id"
        :model-or-class-name="$modelOrClassName"
        :single-media="$singleMedia"
        :collections="$collections"
        :options="$getOptions()"
        title="Media carousel"/>
@endif
<x-mle-shared-assets
    include-css="true"
    include-js="true"
    include-carousel-js="true"
    include-debug-toggle-js="{{ config('medialibrary-extensions.debug') }}"
    include-lite-youtube="{{ config('medialibrary-extensions.youtube_support_enabled') }}"
    :frontend-theme="$getConfig('frontendTheme')"
    for="bootstrap-5|media-carousel-permanent"
/>