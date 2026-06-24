@forelse($media as $medium)
    <div
        id="{{ $domId . '-' . $loop->index }}"
        {{ $attributes->class([
            'mle-component',
            'mle-theme-' . $getConfig('frontendTheme'),
            'mle-media-preview-container',
        ]) }}
        data-mle-media-preview-container
    >
        <x-mle-media-preview-item
            :id="$domId . '-' . $loop->index"
            :media-manager-dom-id="$mediaManagerDomId"
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
        />
        <x-mle-media-modal
            :id="$domId . '-' . $loop->index"
            :model-or-class-name="$modelOrClassName"
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
                :id="$domId . '-' . $loop->index"
                :media-manager-dom-id="$mediaManagerDomId"
                :medium="$medium"
                :model-or-class-name="$modelOrClassName"
                :collections="$collections"
                :single-media="$singleMedia"
                :options="$getOptions()"
                :disabled="$disabled"
                :selectable="$selectable"
                :instance-id="$instanceId"
                :data-source="$getConfig('dataSource')"
            />
        @endif
    </div>
@empty
    <x-mle-media-preview-item-empty />
@endforelse