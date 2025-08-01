<x-media-library-extensions::partial.conditional-form
    :use-xhr="$useXhr"
    :form-attributes="[
        'action' => route(mle_prefix_route('temporary-upload-set-as-first'), $medium),
        'method' => 'POST'
    ]"
    :div-attributes="[
        'data-xhr-form' => true, 
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
{{--    <input type="hidden"--}}
{{--           name="model_type"--}}
{{--           value="{{ get_class($model) }}">--}}
{{--    <input type="hidden"--}}
{{--           name="model_id"--}}
{{--           value="{{ $model->id }}">--}}
    <input type="hidden"
           name="initiator_id"
           value="{{ $id }}">
    <button
        type="{{ $useXhr ? 'button' : 'submit' }}"
        class="mle-button mle-button-icon"
        title="{{ __('media-library-extensions::messages.setup_as_main') }}"
        data-action="temporary-upload-set-as-first"
    >
        <x-mle-partial-icon
            name="{{ config('media-library-extensions.icons.setup_as_main') }}"
            title="{{ __('media-library-extensions::messages.setup_as_main') }}"
        />
    </button>
</x-media-library-extensions::partial.conditional-form>
@if($useXhr)
    <x-mle-partial-assets include-css="true" include-js="true" include-form-submitter="true" :frontend-theme="$theme"/>
@endif

    