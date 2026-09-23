<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => ($multiple ? route(mle_prefix_route('media-upload-multiple')) : route(mle_prefix_route('media-upload-single'))) . '#' . $id,
        'method' => 'POST',
        'enctype' => 'multipart/form-data',
        'data-mle-onsubmit-message' => !$getConfig('useXhr') ? __('medialibrary-extensions::messages.please_wait') : null,
        'data-mle-form'
    ]"
    :div-attributes="[
        'data-mle-xhr-form' => $getConfig('useXhr'), 
    ]"
    method="post"
    class="mle-media-manager-upload-form"
    id="{{ $getDomId() }}"
>
    <label for="{{ $id }}-media-input" class="mle-label">
        {{ __('medialibrary-extensions::messages.upload') }}
        @if($required || $getConfig('minMediaCount') > 0)
            <span class="mle-required-indicator">*</span>
        @endif
    </label>
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
        @mleFormIsolation
        >
        <span class="mle-form-text form-text">
            {{ __('medialibrary-extensions::messages.supported_files', [
                'summary' => $getSupportedFilesSummary(),
            ]) }}
        </span>
        @if(app()->isLocal() || app()->environment('testing'))
            @if ($isLimitedByServerConfiguration())
                <div class="alert alert-warning small mt-2">
                    {{ __('medialibrary-extensions::messages.server_upload_limit_warning', [
                        'size' => $getServerUploadLimit(),
                    ]) }}
                </div>
            @endif
        @endif
    <br>
    @foreach($collections as $collectionType => $collectionName)
        @if (!empty($collectionName))
            <input
                type="hidden"
                name="collections[{{ $collectionType }}]"
                value="{{ $collectionName }}"
                @mleFormIsolation>
        @endif
    @endforeach
    <input
        type="hidden"
        name="_token"
        value="{{ csrf_token() }}"
        @mleFormIsolation>
    <input
        type="hidden"
        name="single_media_id"
        value="{{ $singleMedia?->id || null }}"
        @mleFormIsolation>
    <input 
        type="hidden" 
        name="temporary_upload_mode" 
        value="{{ $temporaryUploadMode ? 'true' : 'false' }}"
        @mleFormIsolation>
    <input
        type="hidden"
        name="model_type"
        value="{{ $modelType }}"
        @mleFormIsolation>
    <input
        type="hidden"
        name="model_id"
        value="{{ $modelId }}"
        @mleFormIsolation>
    <input
        type="hidden"
        name="base_id"
        value="{{ $id }}"
        @mleFormIsolation>
    <input
        type="hidden"
        name="client_token"
        value="{{ $clientToken }}"
        @mleFormIsolation>
    <input type="hidden"
           name="data_source"
           value="{{ $getConfig('dataSource') }}"
           @mleFormIsolation>
    <button
        type="{{ $getConfig('useXhr') ? 'button' : 'submit' }}"
        class="mle-button mle-button-submit mle-upload-button"
        data-mle-action="upload-media"
        data-mle-media-upload-button
        @disabled($disabled)
        @mleFormIsolation
        formaction="{{ ($multiple ? route(mle_prefix_route('media-upload-multiple')) : route(mle_prefix_route('media-upload-single'))) . '#' . $id }}"
        formmethod="POST"
        formenctype="multipart/form-data"
    >
        {{ $multiple
         ? __('medialibrary-extensions::messages.upload_media')
         : __('medialibrary-extensions::messages.upload_medium') }}
    </button>
</x-mle-shared-conditional-form>
@if($getConfig('useXhr'))
    <x-mle-shared-assets 
        :include-css="true" 
        :include-js="true" 
        :include-media-manager-submitter="true" 
        :theme="$getConfig('theme')"
        for="plain|upload-form"
    />
@endif