<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => route(mle_prefix_route('media-upload-youtube')),
        'method' => 'POST',
        'data-form'
    ]"
    :div-attributes="[
        'data-xhr-form' => $getConfig('useXhr'), 
        'id' => $id.'-youtube-upload-form'
    ]"
    method="post"
    class="media-manager-youtube-upload-form"
>
    <input 
        type="hidden" 
        name="temporary_upload_mode" 
        value="{{ $getConfig('temporaryUploadMode') ? 'true' : 'false' }}">
    <input
        type="hidden"
        name="single_medium_id"
        value="{{ $singleMedium?->id || null}}">
    @foreach($collections as $collectionType => $collectionName)
        @if (!empty($collectionName))
            <input
                type="hidden"
                name="collections[{{ $collectionType }}]"
                value="{{ $collectionName }}">
        @endif
    @endforeach
    <input
        type="hidden"
        name="youtube_collection"
        value="{{ $getConfig('youtubeCollection') }}">
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
    <input
        type="hidden"
        name="media_manager_id"
        value="{{ $mediaManagerId }}">
    <input
        type="hidden"
        name="multiple"
        value="{{ $multiple ? 'true' : 'false' }}">
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
        placeholder="https://www.youtube.com/watch?v=..."
        @disabled($disabled)
    >
    <button
        type="{{ $getConfig('useXhr') ? 'button' : 'submit' }}"
        class="mle-button mle-button-submit mle-upload-button"
        data-action="upload-youtube-medium"
        @disabled($disabled)
    >
        {{ __('media-library-extensions::messages.add_youtube_video') }}
    </button>
</x-mle-shared-conditional-form>
@if($getConfig('useXhr'))
    <x-mle-shared-assets 
        include-css="true" 
        include-js="true" 
        include-form-submitter="true" 
        :frontend-theme="$getConfig('frontendTheme')"
    />
@endif