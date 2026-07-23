<!-- used as fallback when not using XHR -->
<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="
    [
        'action' => $getConfig('storeUpdatedMediaRoute'),
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
           value="{{ $medium->id }}">
    <input
        type="hidden"
        name="single_media_id"
        value="{{ $singleMedia?->id || null }}">
    <input type="hidden"
           name="model_type"
           value="{{ $modelType }}">
    <input type="hidden"
           name="model_id"
           value="{{ $modelId }}">
    <input type="hidden"
           name="client_token"
           value="{{ $clientToken }}">
    <input type="hidden"
           name="instance_id"
           value="{{ $instanceId }}">
    <input type="hidden"
           name="base_id"
           value="{{ $id }}">
    <input type="hidden"
           name="temporary_upload_mode"
           value="{{ $temporaryUploadMode ? 'true' : 'false' }}">
    <input type="hidden"
           name="collection"
           value="{{ $medium->collection_name }}">
    <input type="file"
           name="file"
           data-mle-image-editor-update-form-file
           hidden
           aria-label="Upload image"
    >
    <input type="hidden"
           name="data_source"
           value="{{ $getConfig('dataSource') }}">
    @foreach($collections as $collectionType => $collectionName)
        @if (!empty($collectionName))
            <input
                type="hidden"
                name="collections[{{ $collectionType }}]"
                value="{{ $collectionName }}">
        @endif
    @endforeach
    <button
        type="{{ $getConfig('useXhr') ? 'button' : 'submit' }}"
        class="mle-button mle-button-submit mle-button-icon btn btn-primary"
        title="{{ __('medialibrary-extensions::messages.setup_as_main') }}"
        data-mle-action="set-as-first"
        @disabled($disabled)
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