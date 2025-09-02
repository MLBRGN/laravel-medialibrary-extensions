{{--    @dd($modelOrClassName)--}}
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('insert-selected').addEventListener('click', () => {
                // TinyMCE injects a global `tinymce` object in the iframe window
                // so you can do:
                tinymce = window.parent.tinymce;
                console.log('tinymce:', tinymce);

                const selected = Array.from(document.querySelectorAll('.mle-media-select-checkbox:checked'))
                    .map(checkbox => ({ url: checkbox.dataset.url, alt: checkbox.dataset.alt || '' }));

                if (!selected.length) {
                    tinymce.activeEditor.windowManager.alert('Please select one image.');
                    return;
                }
                if (selected.length > 1) {
                    tinymce.activeEditor.windowManager.alert('Please select only one image.');
                    return;
                }

                // Always pick only the first selected image
                const file = selected[0];
                console.log('Selected file:', file);

                // Close the popup if needed
                window.close();
                console.log('Selected', selected);

                // TinyMCE will catch this in onMessage
                window.parent.postMessage({
                    mce: true,
                    content: file
                }, '*'); // use '*' for now

                console.log(window.parent.tinymce?.activeEditor?.windowManager?.params)
            });
        });
    </script>