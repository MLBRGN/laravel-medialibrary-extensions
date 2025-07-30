<x-media-library-extensions::partial.conditional-form
    :use-xhr="$useXhr"
    :form-attributes="[
        'action' => $multiple ? route(mle_prefix_route('media-upload-multiple')) : route(mle_prefix_route('media-upload-single')),
        'method' => 'POST',
        'enctype' => 'multipart/form-data'
    ]"
    :div-attributes="[
        'data-xhr-form' => true, 
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
        >
    
    <span class="mle-form-text form-text">{{ __('media-library-extensions::messages.supported_file_formats_:supported_formats', ['supported_formats' => $allowedMimeTypesHuman]) }}</span>
    <input
        type="hidden"
        name="image_collection"
        value="{{ $imageCollection }}">
    @if($documentCollection)
        <input
            type="hidden"
            name="document_collection"
            value="{{ $documentCollection }}">
    @endif
    <input type="hidden" name="temporary_upload" value="{{ $temporaryUpload ? 'true' : 'false' }}"/>
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
        class="mle-button btn btn-primary d-block"
        data-action="upload-media"
    >
        {{ $multiple
         ? __('media-library-extensions::messages.upload_media')
         : trans_choice('media-library-extensions::messages.upload_or_replace', $mediaPresent ? 1 : 0) }}
    </button>
</x-media-library-extensions::partial.conditional-form>
@if($useXhr)
    <x-mle-partial-assets include-css="true" include-js="true" include-form-submitter="true" :frontend-theme="$theme"/>
@endif
