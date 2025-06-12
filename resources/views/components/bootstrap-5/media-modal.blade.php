<div
    {{ $attributes->merge(['class' => "mlbrgn-mle-component media-modal modal fade $sizeClass"]) }}
    id="{{ $id }}"
    tabindex="-1"
    aria-labelledby="{{ $id }}-title"
    aria-hidden="true"
    @if($videoAutoPlay)
        data-video-autoplay=""
    @endif>

    <div class="media-modal-dialog modal-dialog">
        <div class="media-modal-content modal-content justify-content-center">
            <h1 class="media-modal-title visually-hidden" id="{{ $id }}-title">{{ $title }}</h1>
            <div class="media-modal-body modal-body p-0">
                <button
                    type="button"
                    class="media-modal-close-button"
                    data-bs-dismiss="modal"
                    aria-label="Sluit"
                    title="{{ __('media-library-extensions::messages.close') }}">
                    <x-mle-partial-icon
                        name="{{ config('media-library-extensions.icons.close') }}"
                        title="{{ __('media-library-extensions::messages.close') }}"
                    />
                </button>
{{--                    <button--}}
{{--                        type="button"--}}
{{--                        class="media-modal-close-button btn-close"--}}
{{--                        data-bs-dismiss="modal"--}}
{{--                        aria-label="Sluit"--}}
{{--                        title="{{ __('media-library-extensions::messages.close') }}"></button>--}}

                {{-- important set click to open in modal to false otherwise endless inclusion --}}
                <x-mle-media-carousel
                    class="mle-width-100 mle-height-100 purple"
                    id="{{ $id }}"
                    :model="$model"
                    :click-to-open-in-modal="false"
                    :media-collection="$mediaCollection"
                    :media-collections="$mediaCollections"
                    :in-modal="true"
                />
            </div>
        </div>
    </div>
</div>
<x-mle-partial-assets include-css="true" include-js="true"/>

