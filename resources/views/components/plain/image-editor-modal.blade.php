<div
    {{ $attributes->merge(['class' => 'mlbrgn-mle-component image-editor-modal modal fade']) }}
    {{ $attributes->class([
        'mlbrgn-mle-component',
        'theme-'. $theme,
        'image-editor-modal',
        'fade',
        ])->merge() }}
    id="{{ $id }}"
    tabindex="-1"
    role="dialog"
    aria-labelledby="{{ $id }}-title"
    aria-hidden="true"
    data-modal
    data-image-editor-modal
    data-medium-display-name="{{ media_display_name($medium) }}"
    data-medium-path="{{ $medium->getFullUrl() }}"
>
    <div class="image-editor-modal-dialog">
        <div class="image-editor-modal-content">
            <h1 class="image-editor-modal-title mle-visually-hidden" id="{{ $id }}-title">{{ $title }}</h1>
            <div class="image-editor-modal-body">
                <button
                    data-modal-close
                    aria-label="Sluit"
                    type="button"
                    class="image-editor-modal-close-button"
                    title="{{ __('media-library-extensions::messages.close') }}">
                    <x-mle-partial-icon
                        name="{{ config('media-library-extensions.icons.close') }}"
                        title="{{ __('media-library-extensions::messages.close') }}"
                    />
                </button>
                <input type="hidden" class="image-editor-modal-config" value='@json($config)' />
                {{-- instantiated when model opens, just in time --}}
                <div id="image-editor-placeholder" class="image-editor"></div>
{{--                <image-editor --}}
{{--                    id="imageEditor"--}}
{{--                    data-initiator-id="{{ $id }}"--}}
{{--                    data-medium-display-name="{{ media_display_name($medium) }}"--}}
{{--                    data-medium-path="{{ $medium->getFullUrl() }}"--}}
{{--                />--}}
            </div>
        </div>
    </div>
</div>
<x-mle-partial-assets include-css="true" include-js="true" include-image-editor-js="true" :frontend-theme="$theme"/>

