<div
    {{ $attributes->class([
        'mlbrgn-mle-component',
        'theme-'. $getConfig('frontendTheme'),
        'media-modal',
        'modal',
        'fade',
        ])->merge() }}
    id="{{ $id }}"
    tabindex="-1"
    role="dialog"
    @if($title)
        aria-labelledby="{{ $id }}-title"
    @endif
    aria-hidden="true"
    @if($videoAutoPlay)
        data-autoplay=""
    @endif
    data-modal
    data-media-modal
>
    <div class="media-modal-dialog modal-dialog">
        <div class="media-modal-content modal-content">
            @if($title)
                <h1 class="media-modal-title mle-visually-hidden" id="{{ $id }}-title">{{ $title }}</h1>
            @endif
            <div class="media-modal-body modal-body">
                <button
                    type="button"
                    class="media-modal-close-button modal-close-button"
                    data-modal-close
                    aria-label="Sluit"
                    title="{{ __('media-library-extensions::messages.close') }}">
                    <x-mle-shared-icon
                        name="{{ config('media-library-extensions.icons.close') }}"
                        title="{{ __('media-library-extensions::messages.close') }}"
                    />
                </button>
                <x-mle-media-carousel
                    class="mle-width-100 mle-height-100"
                    id="{{ $id }}"
                    :model-or-class-name="$modelOrClassName"
                    :expandable-in-modal="false"
                    :media-collection="$mediaCollection"
                    :media-collections="$mediaCollections"
                    :options="$options"
{{--                    :frontend-theme="$getConfig('frontendTheme')"--}}
                    :in-modal="true"
                />
            </div>
        </div>
    </div>
</div>
<x-mle-shared-assets 
    include-css="true" 
    include-js="true" 
    :frontend-theme="$getConfig('frontendTheme')"
/>

