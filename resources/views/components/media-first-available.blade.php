@if ($medium)
    <div class="mle-component mle-media-first-available">
        <x-mle-media-viewer
            :medium="$medium"
            :options="$options"
            :preview-mode="true"
            :expandable-in-modal="false"
        />
    </div>
@else
    <div class="mle-component mle-media-placeholder">
        <span>{{ __('media-library-extensions::messages.no_medium') }}</span>
    </div>
@endif
<x-mle-shared-assets 
    include-css="true" 
    include-js="false" 
    include-lite-youtube="true" 
    :frontend-theme="$getConfig('frontendTheme')"
/>
