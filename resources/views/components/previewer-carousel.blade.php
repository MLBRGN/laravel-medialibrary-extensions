{{--
Documentation:
-   when passed an attribute "autoplay" this attribute is passed on to media-preview-modal and
    will cause any youtube video to start autoplaying,
    playing stops when closing the dialog or sliding to another slide
--}}
@props([
    'model' => null, // required
    'mediaCollectionName' => null, // required
    'youtubeCollectionName' => null,
    'logoCollectionName' => null,
    'modalId' => 'media-previewer-modal',
])
@if(is_null($model))
    <p class="text-warning">No model provided!</p>
@elseif(is_null($mediaCollectionName))
    <p class="text-warning">No mediaCollectionName provided!</p>
@else
    {{-- NOTE: wrapper is needed for when component is used in flexbox--}}
    {{-- otherwise the model will also be laid out as flex item;--}}
    <div class="w-100 h-100">
        @php
            // Combine the media items from the collections
            $mediaItems = $model->getMedia($mediaCollectionName);

            // Prepend the enterprise logo if it exists
            if (!is_null($logoCollectionName)) {
                $enterpriseLogo = $model->getMedia($logoCollectionName);
                if ($enterpriseLogo->isNotEmpty()) {
                    $mediaItems = $enterpriseLogo->concat($mediaItems); // logo goes first
                }
            }

            // Append YouTube media if it exists
            if (!is_null($youtubeCollectionName)) {
                $mediaItems = $mediaItems->concat($model->getMedia($youtubeCollectionName));
            }
        @endphp

        <div id="previewer-media-carousel" class="media-preview-carousel carousel slide">
            <div class="carousel-inner">
                @foreach($mediaItems as $index => $medium)
                    <div @class([
                    'carousel-item',
                    'active' => $loop->first,
//                    'd-flex', 'justify-content-center', 'align-items-center', // <-- Add these classes
//                    'h-100', // Ensure it fills the height
                    ]) data-bs-toggle="modal" data-bs-target="#{{$modalId}}">
                        <a class="cursor-zoom-in" data-bs-target="#{{ $modalId }}-carousel"
                           data-bs-slide-to="{{ $index }}">
                            @if($medium->hasCustomProperty('youtube-id'))
                                <div class="youtube-video">
                                    <svg class="youtube-play-button" width="32" height="32">
                                        <use href="/images/svg-sprites/bootstrap-icons-sprite.svg#play-btn-fill"></use>
                                    </svg>
                                    {{ $medium->img()->lazy()->attributes(['class' => 'object-fit-contain']) }}
                                </div>
                            @else
                                <x-media-library-extensions::library-image class="" :media="$medium" conversion="16x9" sizes="(min-width:768px) 245px, (min-width:992px) 245px, (min-width: 1250px) 325, (min-width: 1400px) 365px, 100vw" />
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>
                <button @class([
                    'carousel-control-prev',
                    'disabled' => count($mediaItems) <= 1
                    ]) type="button" data-bs-target="#previewer-media-carousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button @class([
                    'carousel-control-next',
                    'disabled' => count($mediaItems) <= 1
                    ]) type="button" data-bs-target="#previewer-media-carousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
        </div>

    </div>

    <x-media-library-extensions::preview-modal {{ $attributes->merge() }} :modal-id="$modalId" :model="$model" :media-collection-name="$mediaCollectionName" :youtube-collection-name="$youtubeCollectionName" :logo-collection-name="$logoCollectionName" title="Media carousel"/>
@endif
