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
    'modalId' => 'media-previewer-modal',
])
@if(is_null($model))
    <p class="text-warning">No model provided!</p>
@elseif(is_null($mediaCollectionName))
    <p class="text-warning">No mediaCollectionName provided!</p>
@else
    <div class="container-break-out media-manager-preview-images d-flex flex-wrap justify-content-center align-items-center bg-secondary p-5 gap-3 mb-5"  data-bs-toggle="modal" data-bs-target="#media-preview-modal">
        @php
            // Combine the media items from both collections
            $mediaItems = $model->getMedia($mediaCollectionName);
            if (!is_null($youtubeCollectionName)) {
                $mediaItems = $mediaItems->concat($model->getMedia($youtubeCollectionName));
            }
        @endphp

        @foreach($mediaItems as $index => $medium)
            <div class="media-preview-medium-container">
                <div data-bs-toggle="modal" data-bs-target="#{{$modalId}}">
                    <a class="bg-secondary cursor-zoom-in" data-bs-target="#{{ $modalId }}-carousel"
                       data-bs-slide-to="{{ $index }}">
                        @if($medium->hasCustomProperty('youtube-id'))
                            <div class="youtube-video">
                                <svg class="youtube-play-button" width="32" height="32">
                                    <use href="/images/svg-sprites/bootstrap-icons-sprite.svg#play-btn-fill"></use>
                                </svg>
                                {{ $medium->img()->lazy()->attributes(['class' => 'show-flex-item']) }}
                            </div>
                        @else
                            {{ $medium->img()->lazy()->attributes(['class' => 'show-flex-item']) }}
                        @endif

                    </a>
                </div>
            </div>

        @endforeach

    </div>

    <x-media.preview-modal {{ $attributes->merge() }} :modal-id="$modalId" :model="$model" :media-collection-name="$mediaCollectionName" :youtube-collection-name="$youtubeCollectionName" title="Media carousel"/>
@endif
