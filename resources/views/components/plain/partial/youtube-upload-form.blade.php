<x-media-library-extensions::partial.conditional-form
    :use-xhr="$useXhr"
    :form-attributes="[
        'action' => route(mle_prefix_route('media-upload-youtube')),
        'method' => 'POST'
    ]"
    :div-attributes="[
        'data-xhr-form' => true, 
        'id' => $id.'-youtube-upload-form'
    ]"
    class="media-manager-youtube-upload-form"
>
    @csrf
    <input
        type="hidden"
        name="collection_name"
        value="{{ $youtubeCollection }}">
    <input
        type="hidden"
        name="model_type"
        value="{{ get_class($model) }}">
    <input type="hidden" name="temporary_upload" value="{{ $temporaryUpload ? 'true' : 'false' }}"/>
    <input type="hidden" name="temporary_uploads_uuid" value="{{ $temporaryUploadsUuid }}"/>
    <input
        type="hidden"
        name="model_type"
        value="{{ $modelType }}">
    <input
        type="hidden"
        name="model_id"
        value="{{ $modelId }}">
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
</x-media-library-extensions::partial.conditional-form>
@if($useXhr)
    <x-mle-partial-assets include-css="true" include-js="true" include-form-submitter="true" :frontend-theme="$theme"/>
@endif
