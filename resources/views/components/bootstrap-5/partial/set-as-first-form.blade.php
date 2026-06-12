<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => $getConfig('routes.mediumSetAsFirst'),
        'method' => 'POST',
        'data-mle-form'
    ]"
    :div-attributes="[
        'data-mle-xhr-form' => $getConfig('useXhr'), 
        'id' => $id.'-media-set-as-first-form'
    ]"
    method="put"
    class="mle-set-as-first-form"
>
    <input type="hidden"
        name="initiator_id"
        value="{{ $id }}">
    <input type="hidden"
        name="media_manager_id"
        value="{{ $mediaManagerId }}">
    <input type="hidden"
        name="medium_id"
        value="{{ $medium->id }}">
    <input type="hidden"
        name="single_media_id"
        value="{{ $singleMedia?->id || null }}">
    <input type="hidden"
        name="target_media_collection"
        value="{{ $targetMediaCollection }}">
    <input type="hidden"
        name="model_type"
        value="{{ $modelType }}">
    <input type="hidden"
        name="model_id"
        value="{{ $modelId }}">
    <input type="hidden"
        name="temporary_upload_mode"
        value="{{ $temporaryUploadMode ? 'true' : 'false' }}">
    <input type="hidden"
           name="data_source"
           value="{{ $getConfig('dataSource') }}">
    
    @foreach($collections as $collectionType => $collectionName)
        @if (!empty($collectionName))
            <input
                type="hidden"
                name="collections[{{ $collectionType }}]"
                value="{{ $collectionName }}">
        @endif
    @endforeach
    <button
        type="{{ $getConfig('useXhr') ? 'button' : 'submit' }}"
        class="mle-button mle-button-submit mle-button-icon btn btn-primary"
        title="{{ __('medialibrary-extensions::messages.setup_as_main') }}"
        data-mle-action="set-as-first"
        data-mle-route="{{ $getConfig('routes.mediumSetAsFirst') }}"
        data-test="media-set-as-first-button"
        data-test-id="{{ $id }}"
        @disabled($disabled)
    >
        <x-mle-shared-icon
            name="{{ config('medialibrary-extensions.icons.setup_as_main') }}"
            title="{{ __('medialibrary-extensions::messages.setup_as_main') }}"
        />
    </button>
</x-mle-shared-conditional-form>
@if($getConfig('useXhr'))
    <x-mle-shared-assets 
        include-css="true" 
        include-js="true" 
        include-media-manager-submitter="true" 
        :frontend-theme="$getConfig('frontendTheme')"
        for="bootstrap-5|set-as-first-form"
    />
@endif