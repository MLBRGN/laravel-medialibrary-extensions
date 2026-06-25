@if ($componentToRender)
    <x-dynamic-component
        :component="$componentToRender"
        :medium="$medium"
        :options="$getOptions()"
        :preview-mode="$previewMode"
        draggable="{{ isMediaType($medium, 'image') ? 'false' : null }}"
        {{ $attributes->class([
            'mle-media-preview-item',
            'mle-image-responsive' => isMediaType($medium, 'image'),
            'mle-cursor-zoom-in' => $expandableInModal
        ]) }}
        id="{{ $getDomId() }}"
    />
@endif