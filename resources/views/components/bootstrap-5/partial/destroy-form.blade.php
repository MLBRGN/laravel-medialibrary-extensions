<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => $getConfig('mediumDestroyRoute'),
        'method' => 'POST',
        'data-form'
    ]"
    :div-attributes="[
        'data-xhr-form' => $getConfig('useXhr'), 
        'id' => $id.'-media-destroy-form'
    ]"
    method="delete"
    class="media-manager-destroy-form"
>
    <input type="hidden"
        name="initiator_id"
        value="{{ $id }}">
    <input type="hidden"
        name="media_manager_id"
        value="{{ $mediaManagerId }}">
    <input type="hidden"
        name="single_medium_id"
        value="{{ $singleMedium?->id || null }}">
    <input type="hidden"
       name="model_type"
       value="{{ $modelType }}">
    <input type="hidden"
       name="model_id"
       value="{{ $modelId }}">
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
        title="{{ __('media-library-extensions::messages.delete_medium') }}"
        data-action="destroy-medium"
        data-route="{{ $getConfig('mediumDestroyRoute') }}"
        @disabled($disabled)
    >
        <x-mle-shared-icon
            name="{{ config('media-library-extensions.icons.delete') }}"
            :title="__('media-library-extensions::messages.delete_medium')"
        />
    </button>
</x-mle-shared-conditional-form>
@if($getConfig('useXhr'))
    <x-mle-shared-assets 
        include-css="true" 
        include-js="true" 
        include-media-manager-submitter="true" 
        :frontend-theme="$getConfig('frontendTheme')"
    />
@endif