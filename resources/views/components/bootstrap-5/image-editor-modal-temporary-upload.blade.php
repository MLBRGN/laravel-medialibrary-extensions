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
    tabindex="-1"
    @if($title)
        aria-labelledby="{{ $id }}-title"
    @endif
    data-mle-theme="{{$getConfig('theme')}}"
    data-mle-modal
    data-mle-image-editor-modal
    data-base-id="{{ $id }}"
    data-mle-medium-display-name="{{ media_display_name($medium) }}"
    data-mle-medium-path="{{ $medium->getUrl() }}"
    data-mle-medium-forced-aspect-ratio="{{ $forcedAspectRatio }}"
    data-mle-medium-minimal-dimensions="{{ $minimalDimensions }}"
    data-mle-medium-maximal-dimensions="{{ $maximalDimensions }}"
    {{-- no aria-hidden!, role gets added by bs --}}
    
>
    <div class="mle-modal-dialog mle-image-editor-modal-dialog modal-dialog">
        <div class="mle-modal-content mle-image-editor-modal-content modal-content justify-content-center">
            @if($title)
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="{{ $id }}-title">{{ $title }}</h1>
                    <button 
                        type="button" 
                        class="btn-close" 
                        data-bs-dismiss="modal" 
                        data-mle-modal-close 
                        aria-label="{{ __('medialibrary-extensions::messages.close') }}"
                    ></button>
                </div>
            @else
                <h1 class="mle-modal-title mle-visually-hidden" id="{{ $id }}-title">Edit image</h1>
            @endif
            <x-mle-partial-status-area
                id="{{ $id }}"
                :options="$getOptions()"
            />
            <div class="mle-modal-body modal-body p-0">
                @if(!$title)
                    <button
                        type="button"
                        class="btn-close"
                        data-mle-modal-close
                        data-bs-dismiss="modal"
                        aria-label="{{ __('medialibrary-extensions::messages.close') }}"
                        title="{{ __('medialibrary-extensions::messages.close') }}">
                    </button>
                @endif
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
    for="bootstrap-5|image-editor-model-temporary-upload"
/>