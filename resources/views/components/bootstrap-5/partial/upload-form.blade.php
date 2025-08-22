<x-media-library-extensions::shared.conditional-form
    :use-xhr="$useXhr"
    :form-attributes="[
        'action' => $multiple ? route(mle_prefix_route('media-upload-multiple')) : route(mle_prefix_route('media-upload-single')),
        'method' => 'POST',
        'enctype' => 'multipart/form-data'
    ]"
    :div-attributes="[
        'data-xhr-form' => $useXhr, 
        'id' => $id.'-media-upload-form'
    ]"
    method="post"
    class="media-manager-upload-form"
>
    <label for="{{ $id }}-media-input" class="mle-label form-label">Bestanden</label>
    <input
        id="{{ $id }}-media-input"
        accept="{{ $allowedMimeTypes }}"
        type="file"
        class="mle-input form-control"
        @if($multiple)
            name="{{ config('media-library-extensions.upload_field_name_multiple') }}[]"
            multiple 
        @else
            name="{{ config('media-library-extensions.upload_field_name_single') }}"
        @endif
        @disabled($disabled)
        >
    
    <span class="mle-form-text form-text">{{ __('media-library-extensions::messages.supported_file_formats_:supported_formats', ['supported_formats' => $allowedMimeTypesHuman]) }}</span>
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
    <input type="hidden" name="temporary_upload" value="{{ $temporaryUpload ? 'true' : 'false' }}">
    <input
        type="hidden"
        name="model_type"
        value="{{ $modelType }}">
    <input
        type="hidden"
        name="model_id"
        value="{{ $modelId }}">
    <input
        type="hidden"
        name="initiator_id"
        value="{{ $id }}">
    
    <button
        type="{{ $useXhr ? 'button' : 'submit' }}"
        class="mle-button mle-button-submit btn btn-primary d-block"
        data-action="upload-media"
        @disabled($disabled)
    >
        {{ $multiple
         ? __('media-library-extensions::messages.upload_media')
         : __('media-library-extensions::messages.upload_medium') }}
    </button>
</x-media-library-extensions::partial.conditional-form>
@if($useXhr)
    <x-mle-shared-assets include-css="true" include-js="true" include-form-submitter="true" :frontend-theme="$frontendTheme"/>
@endif
