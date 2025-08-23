<x-media-library-extensions::shared.conditional-form
    :use-xhr="$useXhr"
    :form-attributes="
    [
        'action' => $saveUpdatedMediumRoute,
        'method' => 'POST',
        'data-image-editor-update-form' => ''
    ]"
    :div-attributes="[
        'data-xhr-form' => $useXhr, 
        'id' => $id
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
           name="temporary_upload"
           value="{{ $temporaryUpload ? 'true' : 'false' }}">
    <input type="hidden"
           name="collection"
           value="{{ $medium->collection_name }}">
    <input type="file" 
           name="file" 
           hidden>
    @if($imageCollection)
        <input
            type="hidden"
            name="image_collection"
            value="{{ $imageCollection }}">
    @endif
    @if($documentCollection)
        <input
            type="hidden"
            name="document_collection"
            value="{{ $documentCollection }}">
    @endif
    @if($videoCollection)
        <input
            type="hidden"
            name="video_collection"
            value="{{ $videoCollection }}">
    @endif
    @if($audioCollection)
        <input
            type="hidden"
            name="audio_collection"
            value="{{ $audioCollection }}">
    @endif
    @if($youtubeCollection)
        <input
            type="hidden"
            name="youtube_collection"
            value="{{ $youtubeCollection }}">
    @endif
    <button
        type="{{ $useXhr ? 'button' : 'submit' }}"
        class="mle-button mle-button-submit mle-button-icon btn btn-primary"
        title="{{ __('media-library-extensions::messages.setup_as_main') }}"
        data-action="set-as-first"
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