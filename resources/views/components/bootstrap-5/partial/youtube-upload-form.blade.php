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
    method="post"
    class="media-manager-youtube-upload-form"
>
    <input
        type="hidden"
        name="collection_name"
        value="{{ $youtubeCollection }}">
    <input type="hidden" name="temporary_upload" value="{{ $temporaryUpload ? 'true' : 'false' }}"/>
    <input
        type="hidden"
        name="model_type"
        value="{{ $modelType }}">
    <input
        type="hidden"
        name="model_id"
        value="{{ $modelId }}">
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
            @disabled($disabled)
        />
    <button
        type="{{ $useXhr ? 'button' : 'submit' }}"
        class="mle-button btn btn-primary d-block"
        data-action="upload-youtube-medium"
        @disabled($disabled)
    >
        {{ __('media-library-extensions::messages.add_video') }}
    </button>
</x-media-library-extensions::partial.conditional-form>
@if($useXhr)
    <x-mle-partial-assets include-css="true" include-js="true" include-form-submitter="true" :frontend-theme="$frontendTheme"/>
@endif
