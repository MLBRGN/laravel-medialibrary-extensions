<form
    {{ $attributes->class(['media-manager-youtube-upload-form']) }}
    action="{{ route(mle_prefix_route('media-upload-youtube')) }}"
    method="post">
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
        name="target_id"
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
        type="submit"
        class="mle-button mle-upload-button">
        {{ trans_choice('media-library-extensions::messages.upload_or_replace', $mediaPresent ? 1 : 0) }}
    </button>
</form>
