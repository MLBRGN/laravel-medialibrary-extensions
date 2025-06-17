@if($useAjax)
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
<form
    {{ $attributes->class(['media-manager-youtube-upload-form']) }}
    action="{{ route(mle_prefix_route('media-upload-youtube')) }}"
    method="post">
    @csrf
@endif
    <input
        type="hidden"
        name="collection_name"
        value="{{ $youtubeCollection }}">
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
        <label 
            for="{{ $id }}-youtube-url" 
            class="mle-label form-label">
            YouTube Video URL
        </label>
        <input
            id="{{ $id }}-youtube-url"
            type="url" 
            name="youtube_url" 
            class="form-control" 
            placeholder="https://www.youtube.com/watch?v=..." 
        />
    <button
        type="{{ $useAjax ? 'button' : 'submit' }}"
        class="btn btn-primary d-block mt-3">
        {{ __('media-library-extensions::messages.add_video') }}
    </button>
@if($useAjax)
    </div>
    <x-mle-partial-assets include-css="true" include-js="true" include-form-submitter="true"/>
@else
    </form>
@endif
