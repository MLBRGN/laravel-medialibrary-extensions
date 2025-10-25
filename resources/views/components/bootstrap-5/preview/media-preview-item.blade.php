{{--@isset($slot)--}}
{{--    {{ $slot }}--}}
{{--@else--}}
    @switch(true)
        @case(isMediaType($medium, 'youtube-video'))
            <div class="media-manager-preview-item-container" 
                 data-bs-toggle="modal" 
                 data-bs-target="#{{ $id }}-mod"
            >
                <x-mle-video-youtube
                    class="mle-video-youtube mle-video-responsive mle-cursor-zoom-in"
                    data-bs-target="#{{ $id }}-mod-crs"
                    data-bs-slide-to="{{ $loopIndex }}"
                    :medium="$medium"
                    :preview="true"
                    :options="$options"
                />
            </div>
            @break
    
        @case(isMediaType($medium, 'document'))
            <div class="media-manager-preview-item-container" 
                 data-bs-toggle="modal" 
                 data-bs-target="#{{ $id }}-mod"
            >
                <x-mle-document
                    class="previewed-document mle-cursor-zoom-in"
                    data-bs-target="#{{ $id }}-mod-crs"
                    data-bs-slide-to="{{ $loopIndex }}"
                    :medium="$medium"
                    :options="$options"
                />
            </div>
            @break
    
        @case(isMediaType($medium, 'video'))
            <div class="media-manager-preview-item-container" 
                 data-bs-toggle="modal" 
                 data-bs-target="#{{ $id }}-mod"
            >
                <x-mle-video
                    class="mle-cursor-zoom-in"
                    data-bs-target="#{{ $id }}-mod-crs"
                    data-bs-slide-to="{{ $loopIndex }}"
                    :medium="$medium"
                    :options="$options"
                />
            </div>
            @break
    
        @case(isMediaType($medium, 'audio'))
            <div class="media-manager-preview-item-container" 
                 data-bs-toggle="modal" 
                 data-bs-target="#{{ $id }}-mod"
            >
                <x-mle-audio
                    class="mle-cursor-zoom-in"
                    data-bs-target="#{{ $id }}-mod-crs"
                    data-bs-slide-to="{{ $loopIndex }}"
                    :medium="$medium"
                    :options="$options"
                />
            </div>
            @break
    
        @case(isMediaType($medium, 'image'))
            <div class="media-manager-preview-item-container" 
                 data-bs-toggle="modal" 
                 data-bs-target="#{{ $id }}-mod"
            >
                <x-mle-image-responsive
                    class="media-manager-image-preview mle-cursor-zoom-in"
                    data-bs-target="#{{ $id }}-mod-crs"
                    data-bs-slide-to="{{ $loopIndex }}"
                    :medium="$medium"
                    :options="$options"
                    draggable="false"
                />
            </div>
    
            <x-mle-image-editor-modal
                id="{{ $id }}"
                :model-or-class-name="$modelOrClassName"
                :medium="$medium"
                :single-medium="$singleMedium"
                :collections="$collections"
                :options="$options"
                :initiator-id="$id"
                title="Edit Image"
            />
            @break
    
        @default
            <span class="mle-unsupported">{{ __('media-library-extensions::messages.non_supported_file_format') }}</span>
    @endswitch
{{--@endif--}}