@if ($componentToRender)
    <x-dynamic-component
        :id="$id"
        :component="$componentToRender"
        :medium="$medium"
        :options="$getOptions()"
        :preview-mode="$previewMode"
        draggable="{{ isMediaType($medium, 'image') ? 'false' : null }}"
        {{ $attributes->class([
            'mle-media-preview-item' => isMediaType($medium, 'image'),
            'mle-image-responsive' => isMediaType($medium, 'image'),
            'mle-cursor-zoom-in' => $expandableInModal
        ])->merge([
            'data-mle-modal-trigger' => $expandableInModal && $modalId ? '#' . $modalId : null,
        ]) }}
        id="{{ $getDomId() }}"
    />
@endif