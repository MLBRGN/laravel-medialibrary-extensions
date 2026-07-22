@forelse($media as $medium)
    <div
        id="{{ $getDomId() . '-' . $loop->index }}"
        {{ $attributes->class([
            'mle-component',
            'mle-theme-' . $getConfig('theme'),
            'mle-media-preview-container',
        ]) }}
        data-mle-media-preview-container
    >
        <x-mle-media-preview-item
            :id="$id"
            :medium="$medium"
            :options="$getOptions()"
            :collections="$collections"
            :single-media="$singleMedia"
            :model-reference="$modelReference"
            :loop-index="$loop->index"
            :selectable="$selectable"
            :disabled="$disabled"
            :readonly="$readonly"
            :multiple="$multiple"
            :instance-id="$instanceId"
            :data-source="$getConfig('dataSource')"
        />
        <x-mle-media-modal
            :id="$id"
            :model-reference="$modelReference"
            :single-media="$singleMedia"
            :collections="$collections"
            :single-media="$singleMedia"
            :video-auto-play="true"
            :options="$getOptions()"
            title="Media carousel"
            :instance-id="$instanceId"
            :data-source="$getConfig('dataSource')"
            :client-token="$clientToken"
        />
        @if($getConfig('showMenu'))
            <x-mle-media-preview-menu
                :id="$id"
                :medium="$medium"
                :model-reference="$modelReference"
                :collections="$collections"
                :single-media="$singleMedia"
                :options="$getOptions()"
                :disabled="$disabled"
                :selectable="$selectable"
                :multiple="$multiple"
                :instance-id="$instanceId"
                :data-source="$getConfig('dataSource')"
            />
        @endif
    </div>
@empty
    <x-mle-media-preview-item-empty />
@endforelse