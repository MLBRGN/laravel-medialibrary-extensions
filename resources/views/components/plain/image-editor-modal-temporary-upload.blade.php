<div
    {{ $attributes->class([
        'mle-component',
        'mle-theme-'. $getConfig('theme'),
        'mle-image-editor-modal',
        'mle-modal',
        'mle-fade',
    ])->merge() }}
    id="{{ $getDomId() }}"
    tabindex="-1"
    role="dialog"
    @if($title)
        aria-labelledby="{{ $id }}-title"
    @endif
    aria-hidden="true"
    data-mle-theme="{{$getConfig('theme')}}"
    data-mle-modal
    data-mle-image-editor-modal
    data-base-id="{{ $id }}"
    data-mle-medium-display-name="{{ media_display_name($medium) }}"
    data-mle-medium-path="{{ $medium->getUrl() }}"
    data-mle-medium-forced-aspect-ratio="{{ $forcedAspectRatio }}"
    data-mle-medium-minimal-dimensions="{{ $minimalDimensions }}"
    data-mle-medium-maximal-dimensions="{{ $maximalDimensions }}"
>
    <div class="mle-modal-dialog mle-image-editor-modal-dialog">
        <div class="mle-modal-content mle-image-editor-modal-content">
            @if($title)
                <h1 class="mle-modal-title mle-visually-hidden" id="{{ $getDomId() }}-title">{{ $title }}</h1>
            @endif
            <x-mle-partial-status-area
                id="{{ $id }}"
                :options="$getOptions()"
            />
            <div class="mle-modal-body">
                <button
                    type="button"
                    class="mle-modal-close-button"
                    data-mle-modal-close
                    aria-label="Sluit"
                    title="{{ __('medialibrary-extensions::messages.close') }}">
                    <x-mle-shared-icon
                        name="{{ config('medialibrary-extensions.icons.close') }}"
                        title="{{ __('medialibrary-extensions::messages.close') }}"
                    />
                </button>
                <input id="config-{{ $id }}" type="hidden" class="mle-image-editor-modal-config" data-mle-image-editor-modal-config value='@json($getConfig())'>
                {{-- instantiated when model opens, just in time --}}
                <div class="mle-image-editor" data-mle-image-editor-placeholder></div>

                <x-mle-partial-image-editor-form
                    id="{{ $id }}"
                    :model-or-class-name="$modelOrClassName"
                    :medium="$medium"
                    :single-media="$singleMedia"
                    :collections="$collections"
                    :options="$getOptions()"
                    :disabled="$disabled"
                />
            </div>
        </div>
    </div>
</div>
<x-mle-shared-assets 
    :include-css="true" 
    :include-js="true"
    :include-image-editor-js="true"
    :include-image-editor-modal-js="true"
    :theme="$getConfig('theme')"
    for="plain|image-editor-model-temporary-upload"
/>