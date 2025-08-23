<x-media-library-extensions::shared.conditional-form
    :use-xhr="$useXhr"
    :form-attributes="[
        'action' => route(mle_prefix_route('temporary-upload-set-as-first'), $medium),
        'method' => 'POST'
    ]"
    :div-attributes="[
        'data-xhr-form' => $useXhr, 
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
    @if($imageCollection)
        <input
            type="hidden"
            name="image_collection"
            value="{{ $imageCollection }}">
    @endif
    @if($documentCollection)
        <input
            type="hidden"
            name="document_collection"
            value="{{ $documentCollection }}">
    @endif
    @if($videoCollection)
        <input
            type="hidden"
            name="video_collection"
            value="{{ $videoCollection }}">
    @endif
    @if($audioCollection)
        <input
            type="hidden"
            name="audio_collection"
            value="{{ $audioCollection }}">
    @endif
    @if($youtubeCollection)
        <input
            type="hidden"
            name="youtube_collection"
            value="{{ $youtubeCollection }}">
    @endif
    <input type="hidden"
           name="initiator_id"
           value="{{ $id }}">
    <input 
        type="hidden"
        name="media_manager_id"
        value="{{ $mediaManagerId }}">
    <button
        type="{{ $useXhr ? 'button' : 'submit' }}"
        class="mle-button mle-button-submit mle-button-icon btn btn-primary"
        title="{{ __('media-library-extensions::messages.setup_as_main') }}"
        data-action="temporary-upload-set-as-first"
    >
        <x-mle-shared-icon
            name="{{ config('media-library-extensions.icons.setup_as_main') }}"
            title="{{ __('media-library-extensions::messages.setup_as_main') }}"
        />
    </button>
</x-media-library-extensions::partial.conditional-form>
@if($useXhr)
    <x-mle-shared-assets include-css="true" include-js="true" include-form-submitter="true" :frontend-theme="$frontendTheme"/>
@endif

    