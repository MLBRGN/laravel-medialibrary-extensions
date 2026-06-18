<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => $multiple ? route(mle_prefix_route('media-upload-multiple')) : route(mle_prefix_route('media-upload-single')),
        'method' => 'POST',
        'enctype' => 'multipart/form-data',
        'data-mle-form'
    ]"
    :div-attributes="[
        'data-mle-xhr-form' => $getConfig('useXhr'), 
        'id' => $id.'-media-upload-form'
    ]"
    method="post"
    class="mle-media-manager-upload-form"
>
    @if($disabled)
        <div class="mle-alert alert alert-primary">
            @if(!$multiple && $getConfig('disableForm'))
                {{ __('medialibrary-extensions::messages.upload_disabled_only_one_medium_allowed') }}
                {{--                TODO not working correctly--}}
            @elseif($multiple && $getConfig('disableForm'))
                {{ __('medialibrary-extensions::messages.upload_disabled_max_items_reached') }}
            @else
                {{ __('medialibrary-extensions::messages.disabled') }}
            @endif
        </div>
    @endif
    <label for="{{ $id }}-media-input" class="mle-label">Bestanden</label>
    <input
        id="{{ $id }}-media-input"
        data-mle-media-input
        accept="{{ $getConfig('allowedMimeTypes') }}"
        type="file"
        class="mle-input mle-form-control mle-custom-file-input"
        @if($multiple)
            name="media[]"
            multiple
        @else
            name="media"
        @endif
        @disabled($disabled)
        >
    <span class="mle-form-text">{{ __('medialibrary-extensions::messages.supported_file_formats_:supported_formats', ['supported_formats' => $getConfig('allowedMimeTypesHuman')]) }}</span>
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
        name="single_media_id"
        value="{{ $singleMedia?->id || null }}">
    <input 
        type="hidden" 
        name="temporary_upload_mode" 
        value="{{ $temporaryUploadMode ? 'true' : 'false' }}">
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
    <input type="hidden"
           name="data_source"
           value="{{ $getConfig('dataSource') }}">
    <button
        type="{{ $getConfig('useXhr') ? 'button' : 'submit' }}"
        class="mle-button mle-button-submit mle-upload-button"
        data-mle-action="upload-media"
        data-mle-media-upload-button
        @disabled($disabled)
    >
        {{ $multiple
         ? __('medialibrary-extensions::messages.upload_media')
         : __('medialibrary-extensions::messages.upload_medium') }}
    </button>
</x-mle-shared-conditional-form>
@if($getConfig('useXhr'))
    <x-mle-shared-assets 
        include-css="true" 
        include-js="true" 
        include-media-manager-submitter="true" 
        :frontend-theme="$getConfig('frontendTheme')"
        for="plain|upload-form"
    />
@endif