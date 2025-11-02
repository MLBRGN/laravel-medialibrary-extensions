<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => $getConfig('mediumSetAsFirstRoute'),
        'method' => 'POST',
        'data-mle-form'
    ]"
    :div-attributes="[
        'data-mle-xhr-form' => $getConfig('useXhr'), 
        'id' => $id.'-media-set-as-first-form'
    ]"
    method="put"
    class="set-as-first-form"
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
        name="single_medium_id"
        value="{{ $singleMedium?->id || null }}">
    <input type="hidden"
        name="target_media_collection"
        value="{{ $targetMediaCollection }}">
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
        title="{{ __('media-library-extensions::messages.setup_as_main') }}"
        data-mle-action="set-as-first"
        data-mle-route="{{ $getConfig('mediumSetAsFirstRoute') }}"
        @disabled($disabled)
    >
        <x-mle-shared-icon
            name="{{ config('media-library-extensions.icons.setup_as_main') }}"
            title="{{ __('media-library-extensions::messages.setup_as_main') }}"
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