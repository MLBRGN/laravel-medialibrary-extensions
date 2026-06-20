<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => $getConfig('routes.mediumRestore') . '#' . $mediaManagerId,
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
           value="{{ $media->id }}">
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
    <input type="hidden"
           name="data_source"
           value="{{ $getConfig('dataSource') }}">
    <button
        type="submit"
        class="mle-button mle-button-submit mle-button-icon btn btn-primary"
        title="{{ __('medialibrary-extensions::messages.restore_original') }}"
        data-mle-action="medium-restore"
        data-mle-route="{{ $getConfig('routes.mediumRestore') }}"
        data-mle-medium-id="{{ $media->id }}"
    >
        <x-mle-shared-icon
            name="{{ config('medialibrary-extensions.icons.restore') }}"
            :title="__('medialibrary-extensions::messages.restore_original')"
        />
    </button>
</x-mle-shared-conditional-form>
