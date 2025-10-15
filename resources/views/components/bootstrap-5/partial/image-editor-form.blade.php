<!-- used as fallback when not using XHR -->
<x-media-library-extensions::shared.conditional-form
    :use-xhr="$useXhr"
    :form-attributes="
    [
        'action' => $saveUpdatedMediumRoute,
        'method' => 'POST',
        'data-image-editor-update-form' => '',
    ]"
    :div-attributes="[
        'data-xhr-form' => $useXhr, 
    ]"
    id="{{ $id }}"
    method="post"
    class="mle-image-editor-form"
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
    <input type="hidden"
           name="media_manager_id"
           value="{{ $mediaManagerId }}">
    <input type="hidden"
           name="temporary_upload_mode"
           value="{{ $temporaryUploadMode ? 'true' : 'false' }}">
    <input type="hidden"
           name="collection"
           value="{{ $medium->collection_name }}">
    <input type="file"
           name="file"
           hidden>
    @foreach($collections as $collectionType => $collectionName)
        @if (!empty($collectionName))
            <input
                type="hidden"
                name="{{ $collectionType }}_collection"
                value="{{ $collectionName }}">
        @endif
    @endforeach
    <button
        type="{{ $useXhr ? 'button' : 'submit' }}"
        class="mle-button mle-button-submit mle-button-icon btn btn-primary"
        title="{{ __('media-library-extensions::messages.setup_as_main') }}"
        data-action="set-as-first"
        @disabled($disabled)
    >
        <x-mle-shared-icon
            name="{{ config('media-library-extensions.icons.setup_as_main') }}"
            title="{{ __('media-library-extensions::messages.setup_as_main') }}"
        />
    </button>
</x-media-library-extensions::partial.conditional-form>
@if($useXhr)
    <x-mle-shared-assets include-css="true" include-js="true" include-form-submitter="true" :frontend-theme="$frontendTheme"/>
@endif