<div
    id="{{ $id }}-carousel"
    {{ $attributes->merge([
        'class' => 'mlbrgn-mle-component media-manager-preview-modal-carousel'
     ]) }}>
    @if(!$singleMedium)
        <div
            id="{{ $id }}-carousel-indicators"
            class="media-manager-preview-modal-carousel-indicators">
            @foreach($mediaItems as $index => $medium)
                <button
                    type="button"
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
        class="media-manager-preview-modal-carousel-inner">
        @foreach($mediaItems as $index => $medium)
            <div
                @class([
                    'media-manager-preview-modal-carousel-item',
                    'active' => $loop->first
                ])
            >
                <div class="media-manager-preview-modal-carousel-item-wrapper">
                    @if($clickToOpenInModal)
                        <div>
                            <a
                                class="previewed-image cursor-zoom-in">
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
                'media-manager-preview-modal-carousel-control-prev',
                'disabled' => count($mediaItems) <= 1
            ])
            type="button">
            <span
                class="media-manager-preview-modal-carousel-control-prev-icon"
                aria-hidden="true"></span>
            <span class="visually-hidden">{{ __('media-library-extensions::messages.previous') }}</span>
        </button>
        <button
            @class([
                'media-manager-preview-modal-carousel-control-next',
                'disabled' => count($mediaItems) <= 1
            ])
            type="button">
            <span
                class="media-manager-preview-modal-carousel-control-next-icon"
                aria-hidden="true"></span>
            <span class="visually-hidden">Volgende</span>
        </button>
    @endif
    @if($clickToOpenInModal && 3 > 4)
        <x-mle-media-previewer-modal
            :modal-id="$id"
            :model="$model"
            :media-collection="$mediaCollection"
            title="Media carousel"/>
    @endif
</div>
@once
    <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app.css') }}">
@endonce
