<div class="mlbrgn-mle-component">
    <div
        {{ $attributes->merge(['class' => "media-modal modal fade $sizeClass"]) }}
        id="{{ $id }}"
        tabindex="-1"
        aria-labelledby="{{ $id }}-title"
        aria-hidden="true"
        @if($videoAutoPlay)
            data-video-autoplay=""
        @endif>

        <div class="modal-dialog">
            <div class="modal-content justify-content-center">
                <h1 class="modal-title visually-hidden" id="{{ $id }}-title">{{ $title }}</h1>
                <div class="modal-body p-0">
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Sluit"
                        title="{{ __('media-library-extensions::messages.close') }}"></button>

                    {{-- important set click to open in modal to false otherwise endless inclusion --}}
                    <x-mle-media-carousel
                        class="mle-width-100 mle-height-100"
                        id="{{ $id }}"
                        :model="$model"
                        :click-to-open-in-modal="false"
                        :media-collection="$mediaCollection"
                        :media-collections="$mediaCollections"
                        :in-modal="true"/>
                </div>
            </div>
        </div>
    </div>
</div>
<x-mle-partial-assets include-css="true" include-js="true"/>
{{--@once--}}
{{--    @if(config('media-library-extensions.youtube_support_enabled'))--}}
{{--        <script src="https://www.youtube.com/iframe_api"></script>--}}
{{--    @endif--}}
{{--    <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app.css') }}">--}}
{{--@endonce--}}

