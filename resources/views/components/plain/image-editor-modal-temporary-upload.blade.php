<div
    {{ $attributes->class([
        'mle-component',
        'mle-theme-'. $getConfig('frontendTheme'),
        'mle-image-editor-modal',
        'mle-modal',
        'mle-fade',
    ])->merge() }}
    id="{{ $id }}"
    tabindex="-1"
    role="dialog"
    @if($title)
        aria-labelledby="{{ $id }}-title"
    @endif
    aria-hidden="true"
    data-mle-theme="{{$getConfig('frontendTheme')}}"
    data-mle-modal
    data-mle-image-editor-modal
    data-mle-medium-display-name="{{ media_display_name($medium) }}"
    data-mle-medium-path="{{ $medium->getUrl() }}"
    data-mle-medium-forced-aspect-ratio="{{ $forcedAspectRatio }}"
    data-mle-medium-minimal-dimensions="{{ $minimalDimensions }}"
    data-mle-medium-maximal-dimensions="{{ $maximalDimensions }}"
>
    <div class="mle-modal-dialog mle-image-editor-modal-dialog">
        <div class="mle-modal-content mle-image-editor-modal-content">
            @if($title)
                <h1 class="mle-modal-title mle-visually-hidden" id="{{ $id }}-title">{{ $title }}</h1>
            @endif
            <x-mle-partial-status-area
                id="{{ $id }}"
                :initiator-id="$id"
                :media-manager-id="$id"
                :options="$options"
            />
            <div class="mle-modal-body">
                <button
                    type="button"
                    class="mle-modal-close-button"
                    data-mle-modal-close
                    aria-label="Sluit"
                    title="{{ __('media-library-extensions::messages.close') }}">
                    <x-mle-shared-icon
                        name="{{ config('media-library-extensions.icons.close') }}"
                        title="{{ __('media-library-extensions::messages.close') }}"
                    />
                </button>
                <input id="config-{{ $id }}" type="hidden" class="mle-image-editor-modal-config" data-mle-image-editor-modal-config value='@json($config)'>
                {{-- instantiated when model opens, just in time --}}
                <div class="mle-image-editor" data-mle-image-editor-placeholder></div>

                <x-mle-partial-image-editor-form
                    id="{{ $id }}"
                    :model-or-class-name="$modelOrClassName"
                    :medium="$medium"
                    :single-medium="$singleMedium"
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
    for="plain|image-editor-model-temporary-upload"
/>