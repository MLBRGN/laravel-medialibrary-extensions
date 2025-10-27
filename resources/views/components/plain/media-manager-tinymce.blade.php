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
{{--@if($getConfig('frontendTheme') === 'bootstrap-5')--}}
{{--    <link--}}
{{--        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"--}}
{{--        rel="stylesheet"--}}
{{--                    integrity="sha384-QWTKZyjpPEjISv5WaRU5M6QdFVb2l9gCk0GZg6CJWjvvoE5yOAy+n9C80+XW9HdT"--}}
{{--        crossorigin="anonymous"--}}
{{--    >--}}
{{--@endif--}}
