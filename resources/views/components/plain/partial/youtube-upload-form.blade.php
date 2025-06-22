@if($useXhr)
    <div
        id="{{ $id }}-youtube-upload-form"
        class="media-manager-youtube-upload-form"
        data-xhr-form
    >
@else
    <form
        {{ $attributes->class(['media-manager-youtube-upload-form']) }}
        action="{{ route(mle_prefix_route('media-upload-youtube')) }}"
        method="post">
@endif
    @csrf
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
        name="initiator_id"
        value="{{ $id }}">
        <label 
            for="{{ $id }}-youtube-url" 
            class="mle-label">
            YouTube Video URL
        </label>
        <input
            id="{{ $id }}-youtube-url"
            type="url" 
            name="youtube_url" 
            class="mle-input" 
            placeholder="https://www.youtube.com/watch?v=..." />
    <button
        type="{{ $useXhr ? 'button' : 'submit' }}"
        class="mle-button mle-upload-button"
        data-action="upload-youtube-medium"
    >
        {{ trans_choice('media-library-extensions::messages.upload_or_replace', $mediaPresent ? 1 : 0) }}
    </button>
@if($useXhr)
    </div>
    <x-mle-partial-assets include-css="true" include-js="true" include-form-submitter="true" :frontend-theme="$theme"/>
@else
    </form>
@endif
