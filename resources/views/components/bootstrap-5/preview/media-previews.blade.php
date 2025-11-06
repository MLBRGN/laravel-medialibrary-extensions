@forelse($media as $medium)
    <div
        {{ $attributes->class([
            'mle-component',
            'mle-theme-' . $getConfig('frontendTheme'),
            'mle-media-preview-container',
        ]) }}
        data-mle-media-preview-container
    >
        {{-- TODO look at this, without the wrapper flex goes wrong 
            with tall images --}}
        <div class="extra-wrapper-for-flex">
            <x-mle-media-preview-item
                :id="$id"
                :medium="$medium"
                :options="$options"
                :collections="$collections"
                :single-medium="$singleMedium"
                :model-or-class-name="$modelOrClassName"
                :loop-index="$loop->index"
                :selectable="$selectable"
                :disabled="$disabled"
                :readonly="$readonly"
                :multiple="$multiple"
            />
            <x-mle-media-modal
                :id="$id"
                :model-or-class-name="$modelOrClassName"
                :single-medium="$singleMedium"
                :collections="$collections"
                :single-medium="$singleMedium"
                :video-auto-play="true"
                :options="$options"
                title="Media carousel"
            />
        </div>
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