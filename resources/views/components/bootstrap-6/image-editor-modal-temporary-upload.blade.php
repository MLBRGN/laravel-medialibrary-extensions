<div
    {{ $attributes->class([
        'mle-component',
        'mle-theme-'. $getConfig('theme'),
        'mle-image-editor-modal',
        'mle-dialog',
        'modal',
        'fade',
    ])->merge() }}
    id="{{ $getDomId() }}"
    tabindex="-1"
    @if($title)
        aria-labelledby="{{ $id }}-title"
    @endif
    data-mle-theme="{{$getConfig('theme')}}"
    data-mle-dialog
    data-mle-image-editor-modal
    data-base-id="{{ $id }}"
    data-mle-medium-display-name="{{ media_display_name($medium) }}"
    data-mle-medium-path="{{ $medium->getUrl() }}"
    data-mle-medium-forced-aspect-ratio="{{ $forcedAspectRatio }}"
    data-mle-medium-minimal-dimensions="{{ $minimalDimensions }}"
    data-mle-medium-maximal-dimensions="{{ $maximalDimensions }}"
    {{-- no aria-hidden!, role gets added by bs --}}
    
>
    <div class="mle-dialog-dialog mle-image-editor-modal-dialog modal-dialog">
        <div class="mle-dialog-content mle-image-editor-modal-content modal-content justify-content-center">
            @if($title)
                <h1 class="mle-dialog-title mle-visually-hidden" id="{{ $getDomId() }}-title">{{ $title }}</h1>
            @endif
            <x-mle-partial-status-area
                id="{{ $id }}"
                :options="$getOptions()"
            />
            <div class="mle-dialog-body modal-body p-0">
                <button
                    type="button"
                    class="mle-dialog-close-button"
                    data-mle-dialog-close
                    data-bs-dismiss="dialog"
                    aria-label="Sluit"
                    title="{{ __('medialibrary-extensions::messages.close') }}">
                    <x-mle-shared-icon
                        name="{{ config('medialibrary-extensions.icons.close') }}"
                        title="{{ __('medialibrary-extensions::messages.close') }}"
                    />
                </button>
                <input id="config-{{ $id }}" type="hidden" class="mle-image-editor-modal-config" data-mle-media-manager-config value='@json($getConfig())'>
                {{-- instantiated when model opens, just in time --}}
                <div class="mle-image-editor" data-mle-image-editor-placeholder></div>

                <x-mle-partial-image-editor-form
                    id="{{ $id }}"
                    :model-reference="$modelReference"
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
    for="bootstrap-6|image-editor-model-temporary-upload"
/>