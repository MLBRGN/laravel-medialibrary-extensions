<div class="mle-component mle-media-manager-tinymce">
    <x-mle-media-manager
        id="{{ $id }}"
        :model-or-class-name="$modelOrClassName"
        :collections="$collections"
        :options="$getOptions()"
        :multiple="$multiple"
        :selectable="true"
        :readonly="false"
        :disabled="false"
        :instance-id="$instanceId"
        :data-source="$dataSource"
    />
</div>
<x-mle-shared-assets
    :include-css="true"
    :include-js="true"
    :include-tinymce-custom-file-picker-js="true"
    :include-tinymce-custom-file-picker-iframe-js="true"
    :theme="$getConfig('theme')"
    for="plain|media-manager-tinymce"
/>