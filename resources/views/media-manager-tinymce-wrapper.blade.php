<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Media library custom file picker</title>
        <script type="module">
            if (!window.imageEditorLoaded) {
                const script = document.createElement('script');
                script.type = 'module';
                script.src = "{{ asset('vendor/mlbrgn/media-library-extensions/js/root/image-editor.js') }}";
                document.head.appendChild(script);
                window.imageEditorLoaded = true;

                console.log('imageEditorLoaded');
            }
        </script>
    </head>
    <body>
        <div class="mle-component">
            <button type="button" id="insert-selected" class="mle-button mle-button-submit">
                {{ __('media-library-extensions::messages.insert_selected_medium') }}
            </button>
        </div>
        <div class="mle-component mle-media-manager-tinymce">
            <x-mle-media-manager-tinymce
                id="{{ $id }}"
                :model-or-class-name="$modelOrClassName"
                :collections="$collections"
                :options="$options"
                :multiple="$multiple"
                :disabled="false"
                :readonly="false"
                :selectable="true"
            />
        </div>
    </body>
</html>