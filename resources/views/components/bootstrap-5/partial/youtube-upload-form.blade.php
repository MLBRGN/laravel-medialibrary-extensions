@if($useXhr)
    <div
        id="{{ $id }}-youtube-upload-form"
        class="media-manager-youtube-upload-form"
        data-xhr-form
    >
    <x-mle-spinner/>
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
        type="{{ $useXhr ? 'button' : 'submit' }}"
        class="btn btn-primary d-block mt-3"
        data-action="upload-youtube-medium">
        {{ __('media-library-extensions::messages.add_video') }}
    </button>
@if($useXhr)
    </div>
    <x-mle-partial-assets include-css="true" include-js="true" include-form-submitter="true"/>
@else
    </form>
@endif
