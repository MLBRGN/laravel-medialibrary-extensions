@if ($componentToRender)
    <div class="mle-media-preview-item-container"
         id="{{ $id }}"
         data-bs-toggle="modal"
         data-bs-target="#{{ $id }}-mod"
         data-test="media-preview-item"
    >
        <x-mle-media-viewer
            :medium="$medium"
            :options="$getOptions()"
            :preview-mode="true"
            :expandable-in-modal="true"
            data-bs-target="#{{ $id }}-mod-crs"
            data-bs-slide-to="{{ $loopIndex }}"
            :data-source="$getConfig('dataSource')"
        />
    </div>

    @if ($mediumType === 'image')
        <x-mle-image-editor-modal
            id="{{ $id }}"
            :model-or-class-name="$modelOrClassName"
            :medium="$medium"
            :single-media="$singleMedia"
            :collections="$collections"
            :options="$getOptions()"
            :initiator-id="$id"
            title="Edit Image"
            :data-source="$getConfig('dataSource')"
        />
    @endif
@else
    <span class="mle-unsupported">
        {{ __('medialibrary-extensions::messages.non_supported_file_format') }}
    </span>
@endif