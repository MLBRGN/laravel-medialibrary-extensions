@if ($medium)
    <div class="mlbrgn-mle-component">
        @if(isMediaType($medium, 'youtube-video'))
            <x-mle-shared-media-preview-container :id="$id">
                <x-mle-video-youtube
                    class="mle-video-youtube mle-video-responsive"
                    :medium="$medium"
                    :preview="false"
                    data-bs-target="#{{$id}}-modal-carousel"
                    data-bs-slide-to="0"
                />
            </x-mle-shared-media-preview-container>
        @elseif(isMediaType($medium, 'document'))
            <x-mle-shared-media-preview-container :id="$id">
                <x-mle-document 
                    :medium="$medium"
                    class="previewed-document"
                    data-bs-target="#{{ $id }}-modal-carousel"
                    data-bs-slide-to="0"
                />
            </x-mle-shared-media-preview-container>
        @elseif(isMediaType($medium, 'video'))
            <x-mle-shared-media-preview-container :id="$id">
                <x-mle-video
                    :medium="$medium"
                    data-bs-target="#{{ $id }}-modal-carousel"
                    data-bs-slide-to="0"
                />
            </x-mle-shared-media-preview-container>
        @elseif(isMediaType($medium, 'audio'))
            <x-mle-shared-media-preview-container :id="$id">
                <x-mle-audio
                    :medium="$medium"
                    data-bs-target="#{{ $id }}-modal-carousel"
                    data-bs-slide-to="0"
                />
            </x-mle-shared-media-preview-container>
        @elseif(isMediaType($medium, 'image'))
            <x-mle-shared-media-preview-container :id="$id">
                <x-mle-image-responsive
                    :medium="$medium"
                    class="media-preview-image"
                    data-bs-target="#{{$id}}-modal-carousel"
                    data-bs-slide-to="0"
                    draggable="false"
                />
            </x-mle-shared-media-preview-container>
        @else
            {{ __('media-library-extensions::messages.non_supported_file_format') }}
        @endif
    </div>
@else
    <div class="mlbrgn-mle-component mle-media-placeholder">
        <span>{{ __('media-library-extensions::messages.no_medium') }}</span>
    </div>
@endif
<x-mle-shared-assets 
    include-css="true" 
    include-js="false" 
    include-lite-youtube="true" 
    :frontend-theme="$getConfig('frontendTheme')"
/>
