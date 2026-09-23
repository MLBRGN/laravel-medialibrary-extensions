<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => $getConfig('routes.youtubeUpload')  . '#' . $id,
        'method' => 'POST',
        'data-mle-onsubmit-message' => !$getConfig('useXhr') ? __('medialibrary-extensions::messages.please_wait') : null,
        'data-mle-form'
    ]"
    :div-attributes="[
        'data-mle-xhr-form' => $getConfig('useXhr'), 
    ]"
    method="post"
    class="mle-media-manager-youtube-upload-form"
    id="{{ $getDomId() }}"
>
    <input 
        type="hidden" 
        name="temporary_upload_mode" 
        value="{{ $getConfig('temporaryUploadMode') ? 'true' : 'false' }}"
        form="{{ $isolationFormId }}">
    <input
        type="hidden"
        name="single_media_id"
        value="{{ $singleMedia?->id || null}}"
        form="{{ $isolationFormId }}">
    @foreach($collections as $collectionType => $collectionName)
        @if (!empty($collectionName))
            <input
                type="hidden"
                name="collections[{{ $collectionType }}]"
                value="{{ $collectionName }}"
                form="{{ $isolationFormId }}">
        @endif
    @endforeach
    <input
        type="hidden"
        name="youtube_collection"
        value="{{ $getConfig('youtubeCollection') }}"
        form="{{ $isolationFormId }}">
    <input
        type="hidden"
        name="model_type"
        value="{{ $modelType }}"
        form="{{ $isolationFormId }}">
    <input
        type="hidden"
        name="model_id"
        value="{{ $modelId }}"
        form="{{ $isolationFormId }}">
    <input
        type="hidden"
        name="base_id"
        value="{{ $id }}"
        form="{{ $isolationFormId }}">
    <input
        type="hidden"
        name="client_token"
        value="{{ $clientToken }}"
        form="{{ $isolationFormId }}">
    <input
        type="hidden"
        name="multiple"
        value="{{ $multiple ? 'true' : 'false' }}"
        form="{{ $isolationFormId }}">
    <input type="hidden"
           name="data_source"
           value="{{ $getConfig('dataSource') }}"
           form="{{ $isolationFormId }}">
    <label 
        for="{{ $id }}-youtube-url" 
        class="mle-label">
        {{ __('medialibrary-extensions::messages.youtube_video_url') }}
    </label>
    <input
        id="{{ $id }}-youtube-url"
        data-test="youtube-input-{{ $id }}"
        data-mle-youtube-input
        type="url" 
        name="youtube_url" 
        class="mle-input" 
        placeholder="https://www.youtube.com/watch?v=..."
        @disabled($disabled)
        form="{{ $isolationFormId }}"
    >
    <button
        type="{{ $getConfig('useXhr') ? 'button' : 'submit' }}"
        class="mle-button mle-button-submit mle-upload-button"
        data-mle-action="upload-youtube-medium"
{{--        data-test="youtube-upload-button-{{ $id }}"--}}
        data-mle-youtube-upload-button
        @disabled($disabled)
        form="{{ $isolationFormId }}"
    >
        {{ __('medialibrary-extensions::messages.add_youtube_video') }}
    </button>
</x-mle-shared-conditional-form>
@if($getConfig('useXhr'))
    <x-mle-shared-assets 
        :include-css="true" 
        :include-js="true" 
        :include-media-manager-submitter="true" 
        :theme="$getConfig('theme')"
        for="plain|youtube-upload-form"
    />
@endif