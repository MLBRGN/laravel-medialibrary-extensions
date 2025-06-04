<form
    {{ $attributes->class(['media-manager-youtube-upload-form']) }}
    action="{{ route(mle_prefix_route('media-upload-youtube')) }}"
    method="post">
    @csrf
    <input
        type="hidden"
        name="collection_name"
        value="{{ $mediaCollection }}">
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
        <label for="{{ $id }}_youtube_url" class="form-label">YouTube Video URL</label>
        <input type="url" name="youtube_url" id="{{ $id }}_youtube_url" class="form-control" placeholder="https://www.youtube.com/watch?v=..." />
    <button
        type="submit"
        class="btn btn-primary">
        {{ trans_choice('media-library-extensions::messages.upload_or_replace', $mediaPresent ? 1 : 0) }}
    </button>
    <x-mle-partial-flash :target-id="$id"/>
</form>
