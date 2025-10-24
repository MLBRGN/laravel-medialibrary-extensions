@forelse($media as $medium)
    <x-mle-media-preview
        :id="$id"
        :medium="$medium"
        :model-or-class-name="$modelOrClassName"
        :collections="$collections"
        :single-medium="$singleMedium"
        :options="$options"
        :disabled="$disabled"
        :selectable="$selectable"
        :loop-index="$loop->index"
    />
@empty
    <x-mle-media-preview-empty-state />
@endforelse
