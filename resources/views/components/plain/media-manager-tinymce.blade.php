<div class="mlbrgn-mle-component media-manager-tinymce">
    <x-mle-media-manager-multiple
        id="{{ $id }}"
        :model-or-class-name="$modelOrClassName"
        :collections="$collections"
        :options="$options"
        :multiple="$multiple"
        :selectable="true"
        :readonly="false"
        :disabled="false"
    />
</div>
<x-mle-shared-assets
    include-css="true"
    include-js="true"
    include-tinymce-custom-file-picker-js="true"
    include-tinymce-custom-file-picker-iframe-js="true"
    :frontend-theme="$getConfig('frontendTheme')"
/>
