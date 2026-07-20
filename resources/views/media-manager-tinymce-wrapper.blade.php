<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>{{ __('medialibrary-extensions::messages.custom_file_picker') }}</title>
        @php
            $nonce = mlbrgn_csp_nonce();
        @endphp
    </head>
    <body>
        <div class="mle-component">
            <button type="button" id="insert-selected" class="mle-button mle-button-submit" data-mle-insert-selected>
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
                :data-source="$dataSource ?? 'default'"
            />
        </div>
    </body>
</html>