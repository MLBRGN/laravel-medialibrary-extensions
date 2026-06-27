<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Media library custom file picker</title>
        @php
            $nonce = mlbrgn_csp_nonce();
        @endphp
        <script 
            type="module" 
            @isset($nonce) nonce="{{ $nonce }}" @endisset
        >
            if (!window.imageEditorLoaded) {
                const script = document.createElement('script');
                script.type = 'module';
                script.src = "{{ asset(config('medialibrary-extensions.asset_path') . '/js/image-editor.js') }}";
                document.head.appendChild(script);
                window.imageEditorLoaded = true;

                console.log('imageEditorLoaded');
            }
        </script>
    </head>
    <body>
        <div class="mle-component">
            <button type="button" id="insert-selected" class="mle-button mle-button-submit">
                {{ __('medialibrary-extensions::messages.insert_selected_medium') }}
            </button>
        </div>
        <div class="mle-component mle-media-manager-tinymce">
            @php
                $id = isset($id) && $id !== ''
                    ? (string) $id
                    : 'mle-tinymce-'.\Illuminate\Support\Str::uuid()->toString();
            @endphp
            <x-mle-media-manager-tinymce
                id="{{ $id }}"
                :model-or-class-name="$modelOrClassName"
                :collections="$collections"
                :options="$options"
                :multiple="$multiple"
                :disabled="false"
                :readonly="false"
                :selectable="true"
                :data-source="$dataSource ?? null"
            />
        </div>
    </body>
</html>