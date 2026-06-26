@forelse($media as $medium)
    <div
        id="{{ $getDomId() . '-' . $loop->index }}"
        data-base-id="{{ $id }}"
        {{ $attributes->class([
            'mle-component',
            'mle-theme-' . $getConfig('frontendTheme'),
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
            :model-or-class-name="$modelOrClassName"
            :loop-index="$loop->index"
            :selectable="$selectable"
            :disabled="$disabled"
            :readonly="$readonly"
            :multiple="$multiple"
            :instance-id="$instanceId"
            :data-source="$getConfig('dataSource')"
            :client-token="$clientToken"
        />
        <x-mle-media-modal
            :id="$id"
            :model-or-class-name="$modelOrClassName"
            :single-media="$singleMedia"
            :collections="$collections"
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
                :model-or-class-name="$modelOrClassName"
                :collections="$collections"
                :single-media="$singleMedia"
                :options="$getOptions()"
                :disabled="$disabled"
                :selectable="$selectable"
                :instance-id="$instanceId"
                :data-source="$getConfig('dataSource')"
                :client-token="$clientToken"
            />
        @endif
    </div>
@empty
    <x-mle-media-preview-item-empty />
@endforelse