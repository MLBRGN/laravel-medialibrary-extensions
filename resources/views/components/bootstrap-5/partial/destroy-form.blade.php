@if($useXhr)
    <div
        id="{{ $id }}-media-destroy-form"
        data-ajax-upload-form
        class="media-manager-destroy-form"
        data-media-manager-id="{{ $id }}"
    >
@else
    <form
        {{ $attributes->class(['media-manager-preview-form']) }}
        action="{{ $destroyRoute }}"
        method="post">
@endif
        @csrf
        @method('DELETE')
        <input
            type="hidden"
            name="target_id"
            value="{{ $id }}">
        <button
            type="{{ $useXhr ? 'button' : 'submit' }}"
            class="mle-button mle-button-icon btn btn-primary"
            title="{{ __('media-library-extensions::messages.delete_medium') }}"
            data-action="destroy-medium"
          
        >
            <x-mle-partial-icon
                name="{{ config('media-library-extensions.icons.delete') }}"
                :title="__('media-library-extensions::messages.delete_medium')"
            />
        </button>
@if($useXhr)
    </div>
    <x-mle-partial-assets include-css="true" include-js="true" include-form-submitter="true"/>
@else
    </form>
@endif