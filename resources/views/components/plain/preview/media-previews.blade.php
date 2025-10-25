@forelse($media as $medium)
    <div
        {{ $attributes->class([
            'mlbrgn-mle-component',
            'theme-' . $getConfig('frontendTheme'),
            'media-manager-preview-container',
        ]) }}
        data-media-manager-preview-container
    >
        <x-mle-media-preview-item
            :id="$id"
            :medium="$medium"
            :options="$options"
            :collections="$collections"
            :single-medium="$singleMedium"
            :model-or-class-name="$modelOrClassName"
            :loop-index="$loop->index"
        />

        <x-mle-media-modal
            :id="$id"
            :model-or-class-name="$modelOrClassName"
            :media-collections="$collections"
            :video-auto-play="true"
            :options="$options"
            title="Media carousel"
        />

        @if($getConfig('showMenu'))
            <x-mle-media-preview-menu
                :id="$id"
                :medium="$medium"
                :model-or-class-name="$modelOrClassName"
                :collections="$collections"
                :single-medium="$singleMedium"
                :options="$options"
                :disabled="$disabled"
                :selectable="$selectable"
            />
        @endif
    </div>
@empty
    <x-mle-media-preview-item-empty />
@endforelse