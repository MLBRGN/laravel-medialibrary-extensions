@if ($componentToRender)
    <div class="mle-media-preview-item-container"
         data-mle-modal-trigger="#{{$id}}-mod"
         data-mle-slide-to="{{ $loopIndex }}"
    >
        <x-mle-media-viewer
            :medium="$medium"
            :options="$getOptions()"
            :preview-mode="true"
            :expandable-in-modal="true"
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