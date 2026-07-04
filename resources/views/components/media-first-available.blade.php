@if ($medium)
    <div 
        class="mle-component mle-media-first-available"
        id="{{ $getDomId() }}"
    >
        <x-mle-media-viewer
            :medium="$medium"
            :options="$getOptions()"
            :preview-mode="$previewMode"
            :expandable-in-modal="$expandableInModal"
            :data-source="$dataSource"
        />
    </div>
@else
    <div class="mle-component mle-media-placeholder"
         id="{{ $getDomId() }}"
    >
        <span>{{ __('medialibrary-extensions::messages.no_medium') }}</span>
    </div>
@endif
<x-mle-shared-assets 
    include-css="true" 
    include-js="false" 
    include-lite-youtube="true" 
    :frontend-theme="$getConfig('theme')"
    for="shared|media-first-available"
/>
