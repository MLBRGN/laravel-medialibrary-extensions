<div class="mlbrgn-mle-component media-manager-tinymce">
    <x-mle-media-manager-multiple
        :model-or-class-name="$modelOrClassName"
        id="{{ $id }}"
        :image-collection="$imageCollection"
        :document-collection="$documentCollection"
        :youtube-collection="$youtubeCollection"
        :video-collection="$videoCollection"
        :audio-collection="$audioCollection"
        :frontend-theme="$frontendTheme"
        :destroy-enabled="true"
        :set-as-first-enabled="true"
        :show-order="true"
        :show-menu="true"
        :multiple="$temporaryUpload"
        :upload-enabled="true"
        :selectable="true"
    />
</div>
<x-mle-shared-assets
    include-css="true"
    include-js="true"
    :frontend-theme="$frontendTheme"
/>
<script type="module" src="{{ asset('vendor/mlbrgn/media-library-extensions/tinymce-custom-file-picker-iframe.js') }}"></script>