@if (! $noWrapper)
    <div class="media-manager-preview-grid" data-media-manager-preview-grid>
@endif

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
            <x-mle-media-preview-item-empty />
    @endforelse

    <x-mle-media-modal
        :id="$id"
        :model-or-class-name="$modelOrClassName"
        :media-collections="$collections"
        :video-auto-play="true"
        :options="$options"
        title="Media carousel"
    />
@if (! $noWrapper)
    </div>
@endif
