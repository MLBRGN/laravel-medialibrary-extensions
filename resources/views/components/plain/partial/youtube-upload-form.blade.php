<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => route(mle_prefix_route('media-upload-youtube')),
        'method' => 'POST',
        'data-mle-form'
    ]"
    :div-attributes="[
        'data-mle-xhr-form' => $getConfig('useXhr'), 
        'id' => $id.'-youtube-upload-form'
    ]"
    method="post"
    class="mle-media-manager-youtube-upload-form"
>
    @if($disabled)
        <div class="mle-alert alert alert-primary">
            @if(!$multiple && $getConfig('disableForm'))
                {{ __('medialibrary-extensions::messages.upload_disabled_only_one_medium_allowed') }}
            @elseif($multiple && $getConfig('disableForm'))
                {{ __('medialibrary-extensions::messages.upload_disabled_max_items_reached') }}
            @else
                {{ __('medialibrary-extensions::messages.disabled') }}
            @endif
        </div>
    @endif
    <input 
        type="hidden" 
        name="temporary_upload_mode" 
        value="{{ $getConfig('temporaryUploadMode') ? 'true' : 'false' }}">
    <input
        type="hidden"
        name="single_media_id"
        value="{{ $singleMedia?->id || null}}">
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
        name="instance_id"
        value="{{ $getConfig('instanceId') ?? '' }}">
    <input
        type="hidden"
        name="media_manager_id"
        value="{{ $mediaManagerId }}">
    <input
        type="hidden"
        name="client_token"
        value="{{ $clientToken }}">
    <input
        type="hidden"
        name="multiple"
        value="{{ $multiple ? 'true' : 'false' }}">
    <input type="hidden"
           name="data_source"
           value="{{ $getConfig('dataSource') }}">
    <label 
        for="{{ $id }}-youtube-url" 
        class="mle-label">
        YouTube Video URL
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
    >
    <button
        type="{{ $getConfig('useXhr') ? 'button' : 'submit' }}"
        class="mle-button mle-button-submit mle-upload-button"
        data-mle-action="upload-youtube-medium"
        data-test="youtube-upload-button-{{ $id }}"
        @disabled($disabled)
    >
        {{ __('medialibrary-extensions::messages.add_youtube_video') }}
    </button>
</x-mle-shared-conditional-form>
@if($getConfig('useXhr'))
    <x-mle-shared-assets 
        include-css="true" 
        include-js="true" 
        include-media-manager-submitter="true" 
        :frontend-theme="$getConfig('frontendTheme')"
        for="plain|youtube-upload-form"
    />
@endif