@if($setAsFirstEnabled)
    @if($medium->order_column === $media->min('order_column'))
        <button
            class="mle-button mle-button-icon btn btn-primary"
            title="{{ __('media-library-extensions::messages.set-as-main') }}"
            disabled>
            <x-mle-partial-icon
                name="{{ config('media-library-extensions.icons.set-as-main') }}"
                title="{{ __('media-library-extensions::messages.medium_set_as_main') }}"
            />
        </button>
    @else
        <form
            class="media-manager-menu-form"
            action="{{ mle_prefix_route('set-as-first', $medium) }}"
            method="post">
            @csrf
            <input type="hidden"
                   name="medium_id"
                   value="{{ $medium->id }}">
            <input type="hidden"
                   name="collection_name"
                   value="{{ $mediaCollection }}">
            <input type="hidden"
                   name="model_type"
                   value="{{ get_class($model) }}">
            <input type="hidden"
                   name="model_id"
                   value="{{ $model->id }}">
            <input type="hidden"
                   name="target_id"
                   value="{{ $id }}">
            <button
                type="submit"
                class="mle-button mle-button-icon btn btn-primary"
                title="{{ __('media-library-extensions::messages.setup_as_main') }}"
                data-action="set-as-first"
            >
                <x-mle-partial-icon
                    name="{{ config('media-library-extensions.icons.setup_as_main') }}"
                    title="{{ __('media-library-extensions::messages.setup_as_main') }}"
                />
            </button>
        </form>
    @endif
@endif
    