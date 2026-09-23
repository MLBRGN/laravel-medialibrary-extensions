<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => $getConfig('routes.mediaDestroy') . '#' . $id,
        'method' => 'POST',
        'data-mle-onsubmit-message' => !$getConfig('useXhr') ? __('medialibrary-extensions::messages.please_wait') : null,
        'data-mle-form'
    ]"
    :div-attributes="[
        'data-mle-xhr-form' => $getConfig('useXhr'), 
        'id' => $id.'-media-destroy-form'
    ]"
    method="delete"
    class="mle-media-manager-destroy-form"
    id="{{ $getDomId() }}"
>
    <input type="hidden"
        name="base_id"
        value="{{ $id }}"
        form="{{ $isolationFormId }}">
    <input type="hidden"
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
       name="temporary_upload_mode"
       value="{{ $temporaryUploadMode ? 'true' : 'false' }}"
       form="{{ $isolationFormId }}">
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
        class="mle-button mle-button-submit mle-button-icon"
        title="{{ __('medialibrary-extensions::messages.delete_medium') }}"
        data-mle-action="destroy-medium"
        data-mle-route="{{ $getConfig('routes.mediaDestroy') }}"
        data-mle-media-delete-button
        @disabled($disabled)
        form="{{ $isolationFormId }}"
    >
        <x-mle-shared-icon
            name="{{ config('medialibrary-extensions.icons.delete') }}"
            :title="__('medialibrary-extensions::messages.delete_medium')"
        />
    </button>
</x-mle-shared-conditional-form>
@if($getConfig('useXhr'))
    <x-mle-shared-assets 
        :include-css="true" 
        :include-js="true" 
        :include-media-manager-submitter="true"
        :theme="$getConfig('theme')"
        for="plain|destroy-form"
    />
@endif