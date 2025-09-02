<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Component tests: theme bootstrap-5</title>
        <link rel="icon" type="image/x-icon" href="{{ route('mle.favicon') }}">
        {{--    <link--}}
        {{--        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"--}}
        {{--        rel="stylesheet"--}}
        {{--                    integrity="sha384-QWTKZyjpPEjISv5WaRU5M6QdFVb2l9gCk0GZg6CJWjvvoE5yOAy+n9C80+XW9HdT"--}}
        {{--        crossorigin="anonymous"--}}
        {{--    >--}}
    </head>
    <body>
        <div class="mlbrgn-mle-component">
            <button type="button" id="insert-selected" class="mle-button mle-button-submit">
                {{ __('media-library-extensions::messages.insert_selected_medium') }}
            </button>
        </div>
        <div class="mlbrgn-mle-component media-manager-tinymce">
            <x-mle-media-manager-tinymce
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
{{--            <x-mle-media-manager-multiple--}}
{{--                :model-or-class-name="$modelOrClassName"--}}
{{--                id="{{ $id }}"--}}
{{--                :image-collection="$imageCollection"--}}
{{--                :document-collection="$documentCollection"--}}
{{--                :youtube-collection="$youtubeCollection"--}}
{{--                :video-collection="$videoCollection"--}}
{{--                :audio-collection="$audioCollection"--}}
{{--                :frontend-theme="$frontendTheme"--}}
{{--                :destroy-enabled="true"--}}
{{--                :set-as-first-enabled="true"--}}
{{--                :show-order="true"--}}
{{--                :show-menu="true"--}}
{{--                :multiple="$temporaryUpload"--}}
{{--                :upload-enabled="true"--}}
{{--                :selectable="true"--}}
{{--            />--}}
{{--        </div>--}}
{{--        <x-mle-shared-assets --}}
{{--            include-css="true" --}}
{{--            include-js="true" --}}
{{--            :frontend-theme="$frontendTheme"--}}
{{--        />--}}
{{--        <script>--}}
{{--            document.addEventListener('DOMContentLoaded', () => {--}}
{{--                document.getElementById('insert-selected').addEventListener('click', () => {--}}
{{--                    // TinyMCE injects a global `tinymce` object in the iframe window--}}
{{--                    // so you can do:--}}
{{--                    tinymce = window.parent.tinymce;--}}
{{--                    console.log('tinymce:', tinymce);--}}

{{--                    const selected = Array.from(document.querySelectorAll('.mle-media-select-checkbox:checked'))--}}
{{--                        .map(checkbox => ({ url: checkbox.dataset.url, alt: checkbox.dataset.alt || '' }));--}}

{{--                    if (!selected.length) {--}}
{{--                        tinymce.activeEditor.windowManager.alert('Please select one image.');--}}
{{--                        return;--}}
{{--                    }--}}
{{--                    if (selected.length > 1) {--}}
{{--                        tinymce.activeEditor.windowManager.alert('Please select only one image.');--}}
{{--                        return;--}}
{{--                    }--}}

{{--                    // Always pick only the first selected image--}}
{{--                    const file = selected[0];--}}
{{--                    console.log('Selected file:', file);--}}

{{--                    // Close the popup if needed--}}
{{--                    window.close();--}}
{{--                    console.log('Selected', selected);--}}

{{--                    // TinyMCE will catch this in onMessage--}}
{{--                    window.parent.postMessage({--}}
{{--                        mce: true,--}}
{{--                        content: file--}}
{{--                    }, '*'); // use '*' for now--}}

{{--                    console.log(window.parent.tinymce?.activeEditor?.windowManager?.params)--}}
{{--                });--}}
{{--            });--}}
{{--        </script>--}}
    </body>
</html>
