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
        type="{{ $useXhr ? 'button' : 'submit' }}"
        class="mle-button btn btn-primary d-block"
        data-action="upload-youtube-medium"
    >
        {{ __('media-library-extensions::messages.add_video') }}
    </button>
@if($useXhr)
    </div>
    <x-mle-partial-assets include-css="true" include-js="true" include-form-submitter="true" :frontend-theme="$theme"/>
@else
    </form>
@endif
