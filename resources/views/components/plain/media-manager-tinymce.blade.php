<div class="mlbrgn-mle-component media-manager-tinymce">
    <x-mle-media-manager-multiple
        :model-or-class-name="$modelOrClassName"
        id="{{ $id }}"
        :collections="$collections"
        :multiple="$multiple"
        :selectable="true"
        :readonly="false"
        :disabled="false"
        :options="$options"
    />
</div>
<x-mle-shared-assets
    include-css="true"
    include-js="true"
    :frontend-theme="$getConfig('frontendTheme')"
/>
<script type="module" src="{{ asset('vendor/mlbrgn/media-library-extensions/tinymce-custom-file-picker-iframe.js') }}"></script>