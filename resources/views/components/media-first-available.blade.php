@if ($medium)
    <div 
        class="mle-component mle-media-first-available"
        id="{{ $getDomId() }}"
        @if($expandableInModal)
            data-bs-toggle="modal"
            data-bs-target="#{{ $id }}-mod"
        @endif
    >
        <x-mle-media-viewer
            :id="$id"
            :medium="$medium"
            :options="$getOptions()"
            :preview-mode="$previewMode"
            :expandable-in-modal="$expandableInModal"
            :data-source="$dataSource"
        />
    </div>

    @if($expandableInModal)
        <x-mle-media-modal
            :id="$id"
            :model-reference="$modelReference"
            :collections="$collections"
            :options="$getOptions()"
            :instance-id="$instanceId"
            :data-source="$dataSource"
            :client-token="$clientToken"
        />
    @endif
@else
    <div class="mle-component mle-media-placeholder"
         id="{{ $getDomId() }}"
    >
        <span>{{ __('medialibrary-extensions::messages.no_medium') }}</span>
    </div>
@endif
<x-mle-shared-assets 
    :include-css="true" 
    :include-js="false" 
    :include-lite-youtube="true" 
    :theme="$getConfig('theme')"
    for="shared|media-first-available"
/>
