<div
    {{ $attributes->class([
        'mlbrgn-mle-component',
        'theme-'. $getConfig('frontendTheme'),
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
    data-theme="{{$getConfig('frontendTheme')}}"
    data-modal
    data-image-editor-modal
    data-medium-display-name="{{ media_display_name($medium) }}"
    data-medium-path="{{ $medium->getUrl() }}"
    data-medium-forced-aspect-ratio="{{ $forcedAspectRatio }}"
    data-medium-minimal-dimensions="{{ $minimalDimensions }}"
    data-medium-maximal-dimensions="{{ $maximalDimensions }}"
>
    <div class="image-editor-modal-dialog modal-dialog">
        <div class="image-editor-modal-content modal-content">
            @if($title)
                <h1 class="image-editor-modal-title mle-visually-hidden" id="{{ $id }}-title">{{ $title }}</h1>
            @endif
            <x-mle-partial-status-area
                id="{{ $id }}"
                :initiator-id="$id"
                :media-manager-id="$id"
                :options="$options"
            />
            <div class="image-editor-modal-body modal-body">
                <button
                    type="button"
                    class="image-editor-modal-close-button modal-close-button"
                    data-modal-close
                    aria-label="Sluit"
                    title="{{ __('media-library-extensions::messages.close') }}">
                    <x-mle-shared-icon
                        name="{{ config('media-library-extensions.icons.close') }}"
                        title="{{ __('media-library-extensions::messages.close') }}"
                    />
                </button>
                <input id="config-{{ $id }}" type="hidden" class="image-editor-modal-config" data-image-editor-modal-config value='@json($config)'>
                {{-- instantiated when model opens, just in time --}}
                <div class="image-editor" data-image-editor-placeholder></div>
                
                <x-mle-partial-image-editor-form
                    id="{{ $id }}"
                    :model-or-class-name="$modelOrClassName"
                    :medium="$medium"
                    :collections="$collections"
                    :options="$options"
                    :initiator-id="$id"
                    :media-manager-id="$mediaManagerId"
                    :disabled="$disabled"
                />
            </div>
        </div>
    </div>
</div>
<x-mle-shared-assets 
    include-css="true" 
    include-js="true" 
    include-image-editor-js="true"
    include-image-editor-modal-js="true"
    :frontend-theme="$getConfig('frontendTheme')"
/>