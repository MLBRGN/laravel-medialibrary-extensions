@if($useXhr)
    <div
        id="{{ $id }}-media-set-as-first-form"
        class="media-manager-destroy-form"
        data-xhr-form
    >
@else
    <form
        class="media-manager-menu-form"
        action="{{ route(mle_prefix_route('set-as-first'), $medium) }}"
        method="post">
@endif
    @csrf
    <input type="hidden"
           name="medium_id"
           value="{{ $medium->id }}">
    <input type="hidden"
           name="target_media_collection"
           value="{{ $targetMediaCollection }}">
    <input type="hidden"
           name="model_type"
           value="{{ get_class($model) }}">
    <input type="hidden"
           name="model_id"
           value="{{ $model->id }}">
    <input type="hidden"
           name="initiator_id"
           value="{{ $id }}">
    <button
        type="{{ $useXhr ? 'button' : 'submit' }}"
        class="mle-button mle-button-icon btn btn-primary"
        title="{{ __('media-library-extensions::messages.setup_as_main') }}"
        data-action="set-as-first"
    >
        <x-mle-partial-icon
            name="{{ config('media-library-extensions.icons.setup_as_main') }}"
            title="{{ __('media-library-extensions::messages.setup_as_main') }}"
        />
    </button>
        
@if($useXhr)
    </div>
    <x-mle-partial-assets include-css="true" include-js="true" include-form-submitter="true"/>
@else
    </form>
@endif

    