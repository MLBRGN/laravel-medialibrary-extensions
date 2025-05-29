<div
    id="{{ $id }}-carousel"
    {{ $attributes->merge([
        'class' => 'mlbrgn-mle-component media-manager-preview-modal-carousel carousel slide'
     ]) }}>
    @if(!$singleMedium)
        <div
            id="{{ $id }}-carousel-indicators"
            class="media-manager-preview-modal-carousel-indicators carousel-indicators">
            @foreach($mediaItems as $index => $medium)
                <button
                    type="button"
                    data-bs-target="#{{$id}}-carousel"
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
        class="media-manager-preview-modal-carousel-inner carousel-inner"
        data-bs-toggle="modal">
        @foreach($mediaItems as $index => $medium)
            <div
                @class([
                    'media-manager-preview-modal-carousel-item carousel-item',
                    'active' => $loop->first
                ])
            >
                <div class="media-manager-preview-modal-carousel-item-wrapper carousel-item-wrapper">
                    @if($clickToOpenInModal)
                        <div
                            data-bs-toggle="modal"
                            data-bs-target="#{{$id}}">
                            <a
                                class="previewed-image cursor-zoom-in"
                                data-bs-target="#{{$id}}-carousel"
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
                'media-manager-preview-modal-carousel-control-prev carousel-control-prev',
                'disabled' => count($mediaItems) <= 1
            ])
            type="button"
            data-bs-target="#{{$id}}-carousel"
            data-bs-slide="prev">
            <span
                class="media-manager-preview-modal-carousel-control-prev-icon carousel-control-prev-icon"
                aria-hidden="true"></span>
            <span class="visually-hidden">{{ __('media-library-extensions::messages.previous') }}</span>
        </button>
        <button
            @class([
                'media-manager-preview-modal-carousel-control-next carousel-control-next',
                'disabled' => count($mediaItems) <= 1
            ])
            type="button"
            data-bs-target="#{{$id}}-carousel"
            data-bs-slide="next">
            <span
                class="media-manager-preview-modal-carousel-control-next-icon carousel-control-next-icon"
                aria-hidden="true"></span>
            <span class="visually-hidden">Volgende</span>
        </button>
    @endif
    @if($clickToOpenInModal)
        <x-mle-media-previewer-modal
            :id="$id"
            :model="$model"
            :media-collection="$mediaCollection"
            :media-collections="$mediaCollections"
            title="Media carousel"/>
    @endif
</div>

@once
    <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app.css') }}">
    <script src="{{ asset('vendor/media-library-extensions/app.js') }}"></script>
{{--    <script src="https://www.youtube.com/iframe_api"></script>--}}
@endonce
