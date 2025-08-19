<div
    {{ $attributes->class([
    'mlbrgn-mle-component',
    'theme-'. $frontendTheme,
    'image-editor-modal',
    'modal',
    'fade',
    ])->merge() }}
    id="{{ $id }}"
    tabindex="-1"
    role="dialog"
    @if($title)
        aria-labelledby="{{ $id }}-title"
    @endif
    aria-hidden="true"
    data-theme="{{$frontendTheme}}"
    data-image-editor-modal
    data-medium-display-name="{{ media_display_name($medium) }}"
    data-medium-path="{{ $medium->getUrl() }}"
>
    <div class="image-editor-modal-dialog modal-dialog">
        <div class="image-editor-modal-content modal-content justify-content-center">
            @if($title)
                <h1 class="image-editor-modal-title visually-hidden" id="{{ $id }}-title">{{ $title }}</h1>
            @endif
            <div class="image-editor-modal-body modal-body p-0">
                <button
                    type="button"
                    data-modal-close
                    class="image-editor-modal-close-button modal-close-button"
                    data-bs-dismiss="modal"
                    aria-label="Sluit"
                    title="{{ __('media-library-extensions::messages.close') }}">
                    <x-mle-shared-icon
                        name="{{ config('media-library-extensions.icons.close') }}"
                        title="{{ __('media-library-extensions::messages.close') }}"
                    />
                </button>
                <input type="hidden" class="image-editor-modal-config" value='@json($config)'>
                {{-- instantiated when model opens, just in time --}} 
                <div data-image-editor-placeholder class="image-editor"></div>

{{--                old way--}}
{{--                <image-editor --}}
{{--                    id="{{ $id }}-image-editor"--}}
{{--                    data-initiator-id="{{ $id }}"--}}
{{--                    data-medium-display-name="{{ media_display_name($medium) }}"--}}
{{--                    data-medium-path="{{ $medium->getUrl() }}"--}}
{{--                />--}}

            </div>
        </div>
    </div>
</div>
{{--<x-mle-shared-assets include-css="true" include-js="true" include-image-editor-js="true" :frontend-theme="$frontendTheme"/>--}}
<x-mle-shared-assets include-css="true" include-js="true" :frontend-theme="$frontendTheme"/>