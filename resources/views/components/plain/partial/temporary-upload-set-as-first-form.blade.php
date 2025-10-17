<x-media-library-extensions::shared.conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => route(mle_prefix_route('temporary-upload-set-as-first'), $medium),
        'method' => 'POST'
    ]"
    :div-attributes="[
        'data-xhr-form' => $getConfig('useXhr'), 
        'id' => $id.'-media-set-as-first-form'
    ]"
    method="put"
    class="set-as-first-form"
>
    <input type="hidden"
           name="medium_id"
           value="{{ $medium->id }}">
    <input type="hidden"
           name="target_media_collection"
           value="{{ $targetMediaCollection }}">
    @foreach($collections as $collectionType => $collectionName)
        @if (!empty($collectionName))
            <input
                type="hidden"
                name="collections[{{ $collectionType }}]"
                value="{{ $collectionName }}">
        @endif
    @endforeach
    <input type="hidden"
           name="initiator_id"
           value="{{ $id }}">
    <input
        type="hidden"
        name="media_manager_id"
        value="{{ $mediaManagerId }}">
    <button
        type="{{ $getConfig('useXhr') ? 'button' : 'submit' }}"
        class="mle-button mle-button-submit mle-button-icon"
        title="{{ __('media-library-extensions::messages.setup_as_main') }}"
        data-action="temporary-upload-set-as-first"
        @disabled($disabled)
    >
        <x-mle-shared-icon
            name="{{ config('media-library-extensions.icons.setup_as_main') }}"
            title="{{ __('media-library-extensions::messages.setup_as_main') }}"
        />
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

    