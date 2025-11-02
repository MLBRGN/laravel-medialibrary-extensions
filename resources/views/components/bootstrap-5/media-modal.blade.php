<div
    {{ $attributes->class([
        'mle-component',
        'theme-'. $getConfig('frontendTheme'),
        'mle-media-modal',
        'mle-modal',
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
        data-mle-autoplay=""
    @endif
    data-mle-media-modal
>
    <div class="mle-media-modal-dialog mle-modal-dialog modal-dialog">
        <div class="mle-media-modal-content mle-modal-content modal-content justify-content-center">
            @if($title)
                <h1 class="mle-modal-title mle-media-modal-title mle-visually-hidden" id="{{ $id }}-title">{{ $title }}</h1>
            @endif
            <div class="mle-modal-body mle-media-modal-body modal-body p-0">
                <button
                    type="button"
                    data-mle-modal-close
                    class="mle-modal-close-button mle-media-modal-close-button"
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
                    :single-medium="$singleMedium"
                    :expandable-in-modal="false"
{{--                    :media-collection="$mediaCollection"--}}
                    :collections="$mediaCollections"
                    :options="$options"
                    :in-modal="true"
                />
            </div>
        </div>
    </div>
</div>
<x-mle-shared-assets 
    include-css="true" 
    include-js="true"
    include-media-modal-js="true"
    :frontend-theme="$getConfig('frontendTheme')"
/>

