<x-mle-partial-flash :target-id="$id"/>
<form
    {{ $attributes->class(['media-manager-upload-form']) }}
    action="{{ $formAction }}"
    enctype="multipart/form-data"
    method="post"
    @if($useAjax)
        data-ajax="true"
        id="{{ $id.'-form' }}"
        {{--    data-target-id="my-media-manager"--}}
    @endif
    >
    @csrf
    <label for="{{ $id }}-media-input" class="mle-label">Bestanden</label>
    @if($multiple)
        <input
            id="{{ $id }}-media-input"
            accept="{{ $allowedMimeTypes }}"
            name="{{ config('media-library-extensions.upload_field_name_multiple') }}[]"
            type="file"
            class="mle-input custom-file-input"
            multiple>
    @else
        <input
            id="{{ $id }}-media-input"
            accept="{{ $allowedMimeTypes }}"
            name="{{ config('media-library-extensions.upload_field_name_single') }}"
            type="file"
            class="mle-input custom-file-input">
    @endif
    <span class="form-text">{{ __('media-library-extensions::messages.supported_file_formats_:supported_formats', ['supported_formats' => $allowedMimeTypesHuman]) }}</span>
    <input
        type="hidden"
        name="image_collection"
        value="{{ $mediaCollection }}">
    @if($documentCollection)
        <input
            type="hidden"
            name="document_collection"
            value="{{ $documentCollection }}">
    @endif
    <input
        type="hidden"
        name="model_type"
        value="{{ get_class($model) }}">
    <input
        type="hidden"
        name="model_id"
        value="{{ $model->id }}">
    <input
        type="hidden"
        name="target_id"
        value="{{ $id }}">
    <button
        type="submit"
        class="mle-button mle-upload-button">
        {{ $multiple
         ? __('media-library-extensions::messages.upload_media')
         : trans_choice('media-library-extensions::messages.upload_or_replace', $mediaPresent ? 1 : 0) }}
    </button>
</form>
