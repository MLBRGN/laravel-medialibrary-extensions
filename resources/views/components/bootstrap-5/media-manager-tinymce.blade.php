<div class="mlbrgn-mle-component media-manager-tinymce">
    <x-mle-media-manager-multiple
        :model-or-class-name="$modelOrClassName"
        id="{{ $id }}"
        :collections="$collections"
{{--        :frontend-theme="$getConfig('frontendTheme')"--}}
        :show-destroy-button="true"
        :show-set-as-first-button="true"
        :show-media-edit-button="true"
        :show-order="true"
        :show-menu="true"
        :multiple="$multiple"
        :selectable="true"
    />
</div>
<x-mle-shared-assets
    include-css="true"
    include-js="true"
    :frontend-theme="$getConfig('frontendTheme')"
/>
<script type="module" src="{{ asset('vendor/mlbrgn/media-library-extensions/tinymce-custom-file-picker-iframe.js') }}"></script>