<!-- used as fallback when not using XHR -->
<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="
    [
        'action' => $getConfig('routes.storeUpdatedMedia'),
        'method' => 'POST',
        'data-mle-form',
        'data-mle-image-editor-update-form' => '',
    ]"
    :div-attributes="[
        'data-mle-xhr-form' => $getConfig('useXhr'),
    ]"
    method="post"
    class="mle-image-editor-form"
    id="{{ $getDomId() }}"
>
    <input type="hidden"
           name="medium_id"
           value="{{ $medium->id }}"
           form="{{ $isolationFormId }}">
    <input
        type="hidden"
        name="single_media_id"
        value="{{ $singleMedia?->id || null }}"
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
           name="client_token"
           value="{{ $clientToken }}"
           form="{{ $isolationFormId }}">
    <input type="hidden"
           name="instance_id"
           value="{{ $instanceId }}"
           form="{{ $isolationFormId }}">
    <input type="hidden"
           name="base_id"
           value="{{ $id }}"
           form="{{ $isolationFormId }}">
    <input type="hidden"
           name="temporary_upload_mode"
           value="{{ $temporaryUploadMode ? 'true' : 'false' }}"
           form="{{ $isolationFormId }}">
    <input type="hidden"
           name="collection"
           value="{{ $medium->collection_name }}"
           form="{{ $isolationFormId }}">
    <input type="file"
           name="file"
           data-mle-image-editor-update-form-file
           hidden
           aria-label="Upload image"
           form="{{ $isolationFormId }}"
    >
    <input type="hidden"
           name="data_source"
           value="{{ $getConfig('dataSource') }}"
           form="{{ $isolationFormId }}">
    @foreach($collections as $collectionType => $collectionName)
        @if (!empty($collectionName))
            <input
                type="hidden"
                name="collections[{{ $collectionType }}]"
                value="{{ $collectionName }}"
                form="{{ $isolationFormId }}">
        @endif
    @endforeach
    <button
        type="{{ $getConfig('useXhr') ? 'button' : 'submit' }}"
        class="mle-button mle-button-submit mle-button-icon btn btn-primary"
        title="{{ __('medialibrary-extensions::messages.setup_as_main') }}"
        data-mle-action="set-as-first"
        @disabled($disabled)
        form="{{ $isolationFormId }}"
    >
        <x-mle-shared-icon
            name="{{ config('medialibrary-extensions.icons.setup_as_main') }}"
            title="{{ __('medialibrary-extensions::messages.setup_as_main') }}"
        />
    </button>
</x-mle-shared-conditional-form>
@if($getConfig('useXhr'))
    <x-mle-shared-assets 
        :include-css="true" 
        :include-js="true" 
        :include-media-manager-submitter="true" 
        :theme="$getConfig('theme')"
        for="bootstrap-5|image-editor-form"
    />
@endif