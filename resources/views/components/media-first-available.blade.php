@if ($medium)
    <div class="mlbrgn-mle-component">
        @if(isMediaType($medium, 'youtube-video'))
            <div
                class="media-manager-preview-item-container"
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-modal"
            >
                <x-mle-video-youtube
                    class="mle-video-youtube mle-video-responsive mle-cursor-zoom-in"
                    :medium="$medium"
                    :preview="false"
                    data-bs-target="#{{$id}}-modal-carousel"
                    data-bs-slide-to="0"
                />
            </div>
        @elseif(isMediaType($medium, 'document'))
            <div
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-modal"
                class="media-manager-preview-item-container"
            >
                <x-mle-document :medium="$medium"
                                class="previewed-document mle-cursor-zoom-in"
                                data-bs-target="#{{ $id }}-modal-carousel"
                                data-bs-slide-to="0"
                />
            </div>
        @elseif(isMediaType($medium, 'video'))
            <div
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-modal"
                class="media-manager-preview-item-container"
            >
                <x-mle-video
                    :medium="$medium"
                    class="mle-cursor-zoom-in"
                    data-bs-target="#{{ $id }}-modal-carousel"
                    data-bs-slide-to="0"
                />
            </div>
        @elseif(isMediaType($medium, 'audio'))
            <div
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-modal"
                class="media-manager-preview-item-container"
            >
                <x-mle-audio
                    :medium="$medium"
                    class="mle-cursor-zoom-in"
                    data-bs-target="#{{ $id }}-modal-carousel"
                    data-bs-slide-to="0"
                />
            </div>
        @elseif(isMediaType($medium, 'image'))
            <div
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-modal"
                class="media-manager-preview-item-container"
            >
                <x-mle-image-responsive
                    :medium="$medium"
                    class="media-manager-image-preview mle-cursor-zoom-in"
                    data-bs-target="#{{$id}}-modal-carousel"
                    data-bs-slide-to="0"
                    draggable="false"
                />
            </div>
        @else
            {{ __('media-library-extensions::messages.non_supported_file_format') }}
        @endif
    </div>
@endif
<x-mle-partial-assets include-css="true" include-js="false" include-youtube-player="true" :frontend-theme="$frontendTheme"/>
