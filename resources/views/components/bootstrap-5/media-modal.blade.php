<div
    {{ $attributes->class([
        'mlbrgn-mle-component',
        'theme-'. $frontendTheme,
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
>
    <div class="media-modal-dialog modal-dialog">
        <div class="media-modal-content modal-content justify-content-center">
            @if($title)
                <h1 class="media-modal-title visually-hidden" id="{{ $id }}-title">{{ $title }}</h1>
            @endif
            <div class="media-modal-body modal-body p-0">
                <button
                    type="button"
                    data-modal-close
                    class="media-modal-close-button modal-close-button"
                    data-bs-dismiss="modal"
                    aria-label="Sluit"
                    title="{{ __('media-library-extensions::messages.close') }}">
                    <x-mle-shared-icon
                        name="{{ config('media-library-extensions.icons.close') }}"
                        title="{{ __('media-library-extensions::messages.close') }}"
                    />
                </button>
               
                {{-- important set click to open in modal to false otherwise endless inclusion --}}
                <x-mle-media-carousel
                    class="mle-width-100 mle-height-100"
                    id="{{ $id }}"
                    :model-or-class-name="$modelOrClassName"
                    :expandable-in-modal="false"
                    :media-collection="$mediaCollection"
                    :media-collections="$mediaCollections"
                    :frontend-theme="$frontendTheme"
                    :in-modal="true"
                />
            </div>
        </div>
    </div>
</div>
<x-mle-shared-assets include-css="true" include-js="true" :frontend-theme="$frontendTheme"/>

