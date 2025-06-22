@if($useXhr)
    <div
        id="{{ $id }}-media-upload-form"
        class="media-manager-upload-form"
        data-xhr-form
    >
@else
    <form
        {{ $attributes->class(['media-manager-upload-form']) }}
        action="{{ $multiple ? route(mle_prefix_route('media-upload-multiple')) : route(mle_prefix_route('media-upload-single'))}}"
        enctype="multipart/form-data"
        method="post"
    >
@endif
        @csrf
        <label for="{{ $id }}-media-input" class="mle-label form-label">Bestanden</label>
        @if($multiple)
            <input
                id="{{ $id }}-media-input"
                accept="{{ $allowedMimeTypes }}"
                name="{{ config('media-library-extensions.upload_field_name_multiple') }}[]"
                type="file"
                class="mle-input form-control"
                multiple>
        @else
            <input
                id="{{ $id }}-media-input"
                accept="{{ $allowedMimeTypes }}"
                name="{{ config('media-library-extensions.upload_field_name_single') }}"
                type="file"
                class="mle-input form-control">
        @endif
        <span class="mle-form-text form-text">{{ __('media-library-extensions::messages.supported_file_formats_:supported_formats', ['supported_formats' => $allowedMimeTypesHuman]) }}</span>
        <input
            type="hidden"
            name="upload_to_collection"
            value="{{ $uploadToCollection }}">
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
@if($useXhr)
    </div>
    <x-mle-partial-assets include-css="true" include-js="true" include-form-submitter="true" :frontend-theme="$theme"/>
@else
    </form>
@endif
