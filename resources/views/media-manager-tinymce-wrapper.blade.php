<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Component tests: theme bootstrap-5</title>
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