{{--<pre>--}}
{{--        media-carousel--}}
{{--        id - {{ $id }}--}}
{{--    </pre>--}}
<div class="mlbrgn-mle-component" {{ $attributes->merge() }}>
    <div
        id="{{ $id }}"
        @class([
            'media-carousel', 
            'carousel', 
            'slide',
            'carousel-fade' => config('media-library-extensions.carousel_fade')       
         ]) }}>
        @if(!$singleMedium)
            <div
                id="{{ $id }}-indicators"
                class="media-carousel-indicators carousel-indicators">
                @foreach($mediaItems as $index => $medium)
                    <button
                        type="button"
                        data-bs-target="#{{$id}}"
                        data-bs-slide-to="{{ $loop->index }}"
                        {!! $loop->first ? 'aria-current="true"' : '' !!}
                        aria-label="Afbeelding {{ $medium->name }}"
                        @class([
                            'active' => $loop->first
                        ])>
                    </button>
                @endforeach
            </div>
        @endif
        <div
            class="media-carousel-inner carousel-inner"
            data-bs-toggle="modal">
            @foreach($mediaItems as $index => $medium)
                <div
                    @class([
                        'media-carousel-item carousel-item',
                        'active' => $loop->first
                    ])
                >
                    <div class="media-carousel-item-wrapper carousel-item-wrapper">
                        @if($clickToOpenInModal)
                            <div
                                data-bs-toggle="modal"
                                data-bs-target="#{{$id}}-modal">
                                <a
                                    class="previewed-image mle-cursor-zoom-in"
                                    data-bs-target="#{{$id}}-modal-carousel"
                                    data-bs-slide-to="{{ $loop->index }}">
                        @endif
                                    <x-mle-image-responsive
                                        class="image image-zoomed"
                                        :medium="$medium"
                                        :conversions="['thumb', '16x9']"
                                        sizes="95vw"
                                        :alt="$medium->name" />
                        @if($clickToOpenInModal)
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @if(!$singleMedium)
            <button
                @class([
                    'media-carousel-control-prev carousel-control-prev',
                    'disabled' => count($mediaItems) <= 1
                ])
                type="button"
                data-bs-target="#{{$id}}"
                data-bs-slide="prev">
                <span
                    class="media-carousel-control-prev-icon carousel-control-prev-icon"
                    aria-hidden="true"></span>
                <span class="visually-hidden">{{ __('media-library-extensions::messages.previous') }}</span>
            </button>
            <button
                @class([
                    'media-carousel-control-next carousel-control-next',
                    'disabled' => count($mediaItems) <= 1
                ])
                type="button"
                data-bs-target="#{{$id}}"
                data-bs-slide="next">
                <span
                    class="media-carousel-control-next-icon carousel-control-next-icon"
                    aria-hidden="true"></span>
                <span class="visually-hidden">{{ __('media-library-extensions::messages.next') }}</span>
            </button>
        @endif
        @if($clickToOpenInModal)
            <x-mle-media-modal
                :id="$id"
                :model="$model"
    {{--                :media-collection="$mediaCollection"--}}
                :media-collections="$mediaCollections"
                title="Media carousel"/>
        @endif
    </div>
</div>

@once
    <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app.css') }}">
    <script src="{{ asset('vendor/media-library-extensions/app.js') }}"></script>
{{--    <script src="https://www.youtube.com/iframe_api"></script>--}}
@endonce
