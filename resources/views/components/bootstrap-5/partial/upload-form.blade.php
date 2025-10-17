<x-media-library-extensions::shared.conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => $multiple ? route(mle_prefix_route('media-upload-multiple')) : route(mle_prefix_route('media-upload-single')),
        'method' => 'POST',
        'enctype' => 'multipart/form-data'
    ]"
    :div-attributes="[
        'data-xhr-form' => $getConfig('useXhr'), 
        'id' => $id.'-media-upload-form'
    ]"
    method="post"
    class="media-manager-upload-form"
>
    <label for="{{ $id }}-media-input" class="mle-label form-label">Bestanden</label>
    <input
        id="{{ $id }}-media-input"
        accept="{{ $getConfig('allowedMimeTypes') }}"
        type="file"
        class="mle-input form-control"
        @if($multiple)
            name="{{ $getConfig('uploadFieldName') }}[]"
        multiple
        @else
            name="{{$getConfig('uploadFieldName') }}"
        @endif
        @disabled($disabled)
        >
    <span class="mle-form-text form-text">{{ __('media-library-extensions::messages.supported_file_formats_:supported_formats', ['supported_formats' => $getConfig('allowedMimeTypesHuman')]) }}</span>
    @foreach($collections as $collectionType => $collectionName)
        @if (!empty($collectionName))
            <input
                type="hidden"
                name="collections[{{ $collectionType }}]"
                value="{{ $collectionName }}">
        @endif
    @endforeach
{{--    @foreach($collections as $collectionType => $collectionName)--}}
{{--        @if (!empty($collectionName))--}}
{{--            <input--}}
{{--                type="hidden"--}}
{{--                name="{{ $collectionType }}_collection"--}}
{{--                value="{{ $collectionName }}">--}}
{{--        @endif--}}
{{--    @endforeach--}}
    <input
        type="hidden"
        name="medium_id"
        value="{{ $medium ? $medium->id : null }}">
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
        name="media_manager_id"
        value="{{ $mediaManagerId }}">
    <button
        type="{{ $getConfig('useXhr') ? 'button' : 'submit' }}"
        class="mle-button mle-button-submit btn btn-primary d-block"
        data-action="upload-media"
        @disabled($disabled)
    >
        {{ $multiple
         ? __('media-library-extensions::messages.upload_media')
         : __('media-library-extensions::messages.upload_medium') }}
    </button>
</x-media-library-extensions::partial.conditional-form>
@if($getConfig('useXhr'))
    <x-mle-shared-assets 
        include-css="true" 
        include-js="true" 
        include-form-submitter="true" 
        :frontend-theme="$getConfig('frontendTheme')"
    />
@endif
