<div
    {{ $attributes->class([
        'mlbrgn-mle-component',
        'theme-' . $getConfig('frontendTheme'),
        'media-manager-preview-container',
    ]) }}
    data-media-manager-preview-container
>
    {{--    TODO don't want routes here--}}
    <x-mle-media-preview-item
        :id="$id"
        :medium="$medium"
        :options="$options"
        :collections="$collections"
        :single-medium="$singleMedium"
        :model-or-class-name="$modelOrClassName"
        :loop-index="$loopIndex"
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
