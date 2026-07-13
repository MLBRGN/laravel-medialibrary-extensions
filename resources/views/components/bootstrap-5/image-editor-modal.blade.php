<div
    {{ $attributes->class([
        'mle-component',
        'mle-theme-'. $getConfig('theme'),
        'mle-image-editor-modal',
        'mle-modal',
        'modal',
        'fade',
    ])->merge() }}
    id="{{ $getDomId() }}"
    data-base-id="{{ $id }}"
    tabindex="-1"
    @if($title)
        aria-labelledby="{{ $id }}-title"
    @endif
    data-mle-theme="{{$getConfig('theme')}}"
    data-mle-modal
    data-mle-image-editor-modal
    data-mle-medium-display-name="{{ media_display_name($medium) }}"
    data-mle-medium-path="{{ $medium->getUrl() }}"
    data-mle-medium-forced-aspect-ratio="{{ $forcedAspectRatio }}"
    data-mle-medium-minimal-dimensions="{{ $minimalDimensions }}"
    data-mle-medium-maximal-dimensions="{{ $maximalDimensions }}"
    data-test="image-editor-modal"
    data-mle-image-editor-initialized="false"
    {{-- no aria-hidden!, role gets added by bs --}}
>
    <div class="mle-modal-dialog mle-image-editor-modal-dialog modal-dialog">
        <div class="mle-modal-content mle-image-editor-modal-content modal-content justify-content-center">
            @if($title)
                <h1 class="mle-modal-title mle-visually-hidden" id="{{ $id }}-title">{{ $title }}</h1>
            @endif
                <x-mle-partial-status-area
                    id="{{ $id }}"
                    :options="$getOptions()"
                />
            <div class="mle-modal-body modal-body p-0">
                <button
                    type="button"
                    class="mle-modal-close-button"
                    data-mle-modal-close
                    data-bs-dismiss="modal"
                    aria-label="Sluit"
                    title="{{ __('medialibrary-extensions::messages.close') }}"
                >
                    <x-mle-shared-icon
                        name="{{ config('medialibrary-extensions.icons.close') }}"
                        title="{{ __('medialibrary-extensions::messages.close') }}"
                    />
                </button>
                <input id="config-{{ $id }}" type="hidden" class="mle-image-editor-modal-config" data-mle-image-editor-modal-config value='@json($getConfig())'>
                {{-- instantiated when model opens, just in time --}}
                <div class="mle-image-editor" data-mle-image-editor-placeholder>
                    <div class="mle-image-editor-placeholder">
                        {{ __('medialibrary-extensions::messages.could_not_initialize_image_editor') }}
                    </div>
                </div>

                <x-mle-partial-image-editor-form
                    id="{{ $id }}"
                    :model-or-class-name="$modelOrClassName"
                    :medium="$medium"
                    :single-media="$singleMedia"
                    :collections="$collections"
                    :options="$getOptions()"
                    :disabled="$disabled"
                    :data-source="$dataSource"
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
    :theme="$getConfig('theme')"
    for="bootstrap-5|image-editor-model"
/>