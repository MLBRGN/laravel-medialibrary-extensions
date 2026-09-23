<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => $getConfig('routes.mediumRestore') . '#' . $id,
        'method' => 'POST',
        'data-mle-onsubmit-message' => !$getConfig('useXhr') ? __('medialibrary-extensions::messages.please_wait') : null,
        'data-mle-form'
    ]"
    :div-attributes="[
        'data-mle-xhr-form' => $getConfig('useXhr'), 
    ]"
    method="post"
    class="mle-media-lab-restore-form"
    id="{{ $getDomId() }}"
    data-mle-restore-form
>
    <input type="hidden"
           name="medium_id"
           value="{{ $media->id }}"
           form="{{ $isolationFormId }}">
    <input type="hidden"
           name="model_type"
           value="{{ $modelType }}"
           form="{{ $isolationFormId }}">
    <input type="hidden"
           name="model_id"
           value="{{ $modelId }}"
           form="{{ $isolationFormId }}">
    <input type="hidden"
           name="base_id"
           value="{{ $id }}"
           form="{{ $isolationFormId }}">
{{--    <input type="hidden"--}}
{{--           name="collection"--}}
{{--           value="{{ $medium->collection_name }}">--}}
    <input type="hidden"
           name="temporary_upload_mode"
           value="{{ $temporaryUploadMode ? 'true' : 'false' }}"
           form="{{ $isolationFormId }}">
    <input type="hidden"
           name="data_source"
           value="{{ $getConfig('dataSource') }}"
           form="{{ $isolationFormId }}">
    <button
        type="submit"
        class="mle-button mle-button-submit mle-button-icon"
        title="{{ __('medialibrary-extensions::messages.restore_original') }}"
        data-mle-action="medium-restore"
        data-mle-route="{{ $getConfig('routes.mediumRestore') }}"
        data-mle-medium-id="{{ $media->id }}"
        form="{{ $isolationFormId }}"
    >
        <x-mle-shared-icon
            name="{{ config('medialibrary-extensions.icons.restore') }}"
            :title="__('medialibrary-extensions::messages.restore_original')"
        />
    </button>
</x-mle-shared-conditional-form>
