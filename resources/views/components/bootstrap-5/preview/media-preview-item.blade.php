@if ($componentToRender)
    <div class="mle-media-preview-item-container"
         data-bs-toggle="modal"
         data-bs-target="#{{ $id }}-mod"
    >
            <x-dynamic-component
                :component="$componentToRender"
                class="{{ $mediumType === 'image' 
                    ? 'mle-media-preview-image mle-cursor-zoom-in' 
                    : 'mle-cursor-zoom-in' }}"
                data-bs-target="#{{ $id }}-mod-crs"
                data-bs-slide-to="{{ $loopIndex }}"
                :medium="$medium"
                :options="$options"
                :draggable="$mediumType === 'image' ? 'false' : null"
                :preview="true"
            />
    </div>

    @if ($mediumType === 'image')
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
    @endif
@else
    <span class="mle-unsupported">
        {{ __('media-library-extensions::messages.non_supported_file_format') }}
    </span>
@endif