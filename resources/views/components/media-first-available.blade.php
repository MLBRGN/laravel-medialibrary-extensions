@if ($medium)
    <div class="mlbrgn-mle-component">
        @if(isMediaType($medium, 'youtube-video'))
            <div
                class="media-manager-preview-item-container"
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-mod"
            >
                <x-mle-video-youtube
                    class="mle-video-youtube mle-video-responsive"
                    :medium="$medium"
                    :preview="false"
                    data-bs-target="#{{$id}}-modal-carousel"
                    data-bs-slide-to="0"
                />
            </div>
        @elseif(isMediaType($medium, 'document'))
            <div
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-mod"
                class="media-manager-preview-item-container"
            >
                <x-mle-document :medium="$medium"
                                class="previewed-document"
                                data-bs-target="#{{ $id }}-modal-carousel"
                                data-bs-slide-to="0"
                />
            </div>
        @elseif(isMediaType($medium, 'video'))
            <div
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-mod"
                class="media-manager-preview-item-container"
            >
                <x-mle-video
                    :medium="$medium"
                    data-bs-target="#{{ $id }}-modal-carousel"
                    data-bs-slide-to="0"
                />
            </div>
        @elseif(isMediaType($medium, 'audio'))
            <div
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-mod"
                class="media-manager-preview-item-container"
            >
                <x-mle-audio
                    :medium="$medium"
                    data-bs-target="#{{ $id }}-modal-carousel"
                    data-bs-slide-to="0"
                />
            </div>
        @elseif(isMediaType($medium, 'image'))
            <div
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-mod"
                class="media-manager-preview-item-container"
            >
                <x-mle-image-responsive
                    :medium="$medium"
                    class="media-manager-image-preview"
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
<x-mle-shared-assets include-css="true" include-js="false" include-lite-youtube="true" :frontend-theme="$frontendTheme"/>
