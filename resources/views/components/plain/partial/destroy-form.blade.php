<x-media-library-extensions::partial.conditional-form
    :use-xhr="$useXhr"
    :form-attributes="[
        'action' => route(mle_prefix_route('medium-destroy'), $medium),
        'method' => 'POST'
    ]"
    :div-attributes="[
        'data-xhr-form' => true, 
        'id' => $id.'-media-destroy-form'
    ]"
    class="media-manager-destroy-form"
>
    @csrf
    @method('DELETE')
    <input
        type="hidden"
        name="initiator_id"
        value="{{ $id }}">
    <button
        type="{{ $useXhr ? 'button' : 'submit' }}"
        class="mle-button mle-button-icon"
        title="{{ __('media-library-extensions::messages.delete_medium') }}"
        data-action="destroy-medium"
    >
        <x-mle-partial-icon
            name="{{ config('media-library-extensions.icons.delete') }}"
            :title="__('media-library-extensions::messages.delete_medium')"
        />
    </button>
</x-media-library-extensions::partial.conditional-form>
@if($useXhr)
    <x-mle-partial-assets include-css="true" include-js="true" include-form-submitter="true" :frontend-theme="$theme"/>
@endif