<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => $getConfig('mediumRestoreRoute'),
        'method' => 'POST',
        'data-mle-form'
    ]"
    :div-attributes="[
        'data-mle-xhr-form' => $getConfig('useXhr'), 
        'id' => $id.'-media-restore-form'
    ]"
    method="post"
    class="mle-media-lab-restore-form"
>
    <input type="hidden"
           name="medium_id"
           value="{{ $medium->id }}">
    <input type="hidden"
           name="model_type"
           value="{{ $modelType }}">
    <input type="hidden"
           name="model_id"
           value="{{ $modelId }}">
    <input type="hidden"
           name="initiator_id"
           value="{{ $id }}">
{{--    <input type="hidden"--}}
{{--           name="collection"--}}
{{--           value="{{ $medium->collection_name }}">--}}
    <input type="hidden"
           name="temporary_upload_mode"
           value="{{ $temporaryUploadMode ? 'true' : 'false' }}">
    <button
        type="submit"
        class="mle-button mle-button-submit mle-button-icon btn btn-primary"
        title="{{ __('media-library-extensions::messages.restore_original') }}"
        data-mle-action="medium-restore"
        data-mle-route="{{ $getConfig('mediumRestoreRoute') }}"
        data-mle-medium-id="{{ $medium->id }}"
    >
        <x-mle-shared-icon
            name="{{ config('media-library-extensions.icons.restore') }}"
            :title="__('media-library-extensions::messages.restore_original')"
        />
    </button>
</x-mle-shared-conditional-form>
