@if($useXhr)
    <div
        id="{{ $id }}-media-upload-form"
        data-ajax-upload-form
        class="media-manager-upload-form"
        data-media-manager-id="{{ $id }}"
        data-form-action-route="{{ $formActionRoute }}"
        data-preview-refresh-route="{{ $previewRefreshRoute }}"
        data-model-type="{{ $model->getMorphClass() }}"
        data-model-id="{{ $model->getKey() }}"
        data-collection="{{ $mediaCollection }}"
        data-youtube-collection="{{ $youtubeCollection }}"
        data-document-collection="{{ $documentCollection }}"
        data-destroy-enabled="{{ $destroyEnabled ? 'true' : 'false' }}"
        data-set-as-first-enabled="{{ $setAsFirstEnabled ? 'true' : 'false' }}"
        data-csrf-token="{{ csrf_token() }}"
        data-theme="bootstrap-5"
    >
        <div class="mle-spinner-container" data-spinner-container>
            <div class="mle-spinner"></div>
            <div>{{ __('media-library-extensions::messages.please_wait') }}</div>
        </div>
@else
    <x-mle-partial-flash :target-id="$id"/>
    <form
        {{ $attributes->class(['media-manager-upload-form']) }}
        action="{{ $formActionRoute }}"
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
            type="{{ $useXhr ? 'button' : 'submit' }}"
            class="btn btn-primary d-block mt-3">
            {{ $multiple
             ? __('media-library-extensions::messages.upload_media')
             : trans_choice('media-library-extensions::messages.upload_or_replace', $mediaPresent ? 1 : 0) }}
        </button>
@if($useXhr)
    </div>
    <x-mle-partial-assets include-css="true" include-js="true" include-form-submitter="true"/>
@else
    </form>
@endif
