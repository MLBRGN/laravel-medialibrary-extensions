<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => $getConfig('routes.mediaDestroy') . '#' . $mediaManagerDomId,
        'method' => 'POST',
        'data-mle-form'
    ]"
    :div-attributes="[
        'data-mle-xhr-form' => $getConfig('useXhr'), 
        'id' => $id.'-media-destroy-form'
    ]"
    method="delete"
    class="mle-media-manager-destroy-form"
    id="{{ $getDomId() }}"
>
    <input type="hidden"
        name="initiator_id"
        value="{{ $id }}">
    <input type="hidden"
        name="media_manager_id"
        value="{{ $mediaManagerDomId }}">
    <input
        type="hidden"
        name="instance_id"
        value="{{ $getConfig('instanceId') ?? '' }}">
    <input type="hidden"
        name="single_media_id"
        value="{{ $singleMedia?->id || null }}">
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
        title="{{ __('medialibrary-extensions::messages.delete_medium') }}"
        data-mle-action="destroy-medium"
        data-mle-route="{{ $getConfig('routes.mediaDestroy') }}"
        data-mle-media-delete-button
        @disabled($disabled)
    >
        <x-mle-shared-icon
            name="{{ config('medialibrary-extensions.icons.delete') }}"
            :title="__('medialibrary-extensions::messages.delete_medium')"
        />
    </button>
</x-mle-shared-conditional-form>
@if($getConfig('useXhr'))
    <x-mle-shared-assets 
        include-css="true" 
        include-js="true" 
        include-media-manager-submitter="true" 
        :frontend-theme="$getConfig('frontendTheme')"
        for="bootstrap-5|destroy-form"
    />
@endif