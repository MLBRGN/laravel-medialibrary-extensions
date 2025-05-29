<form
        {{ $attributes->class(['media-manager-preview-form media-manager-multiple-preview-form']) }}
        action="{{ route($destroyRoute) }}"
        method="post">
    @csrf
    @method('DELETE')
    <input 
        type="hidden" 
        name="target_id" 
        value="{{ $id }}"/>
    <button
            type="submit"
            class=""
            title="{{ __('media-library-extensions::messages.delete_medium') }}">
        <x-mle_internal-icon
                name="{{ config('media-library-extensions.icons.delete') }}"
                :title="__('media-library-extensions::messages.delete_medium')"
        />
    </button>
{{--    <button--}}
{{--        class="button-icon-delete btn btn-delete btn-icon btn-icon-delete btn-sm">--}}
{{--    </button>--}}
</form>
