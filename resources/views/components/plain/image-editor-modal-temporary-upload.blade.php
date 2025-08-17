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
    aria-labelledby="{{ $id }}-title"
    aria-hidden="true"
    data-theme="{{$frontendTheme}}"
    data-modal
    data-image-editor-modal
    data-medium-display-name="{{ media_display_name($medium) }}"
    data-medium-path="{{ $medium->getUrl() }}"
>
    <div class="image-editor-modal-dialog modal-dialog">
        <div class="image-editor-modal-content modal-content justify-content-center">
            <h1 class="image-editor-modal-title visually-hidden" id="{{ $id }}-title">{{ $title }}</h1>
            <div class="image-editor-modal-body modal-body p-0">
                <button
                    type="button"
                    data-modal-close
                    class="image-editor-modal-close-button modal-close-button"
                    aria-label="Sluit"
                    title="{{ __('media-library-extensions::messages.close') }}">
                    <x-mle-partial-icon
                        name="{{ config('media-library-extensions.icons.close') }}"
                        title="{{ __('media-library-extensions::messages.close') }}"
                    />
                </button>
                <input type="hidden" class="image-editor-modal-config" value='@json($config)' />
                {{-- instantiated when model opens, just in time --}}
                <div id="image-editor-placeholder" class="image-editor"></div>
            </div>
        </div>
    </div>
</div>
{{--<x-mle-partial-assets include-css="true" include-js="true" include-image-editor-js="true" :frontend-theme="$frontendTheme"/>--}}
<x-mle-partial-assets include-css="true" include-js="true" :frontend-theme="$frontendTheme"/>