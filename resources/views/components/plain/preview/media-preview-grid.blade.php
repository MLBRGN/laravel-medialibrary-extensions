<div 
    class="mle-media-preview-grid" 
    data-mle-media-preview-grid
    id="{{ $getDomId() }}"
    data-base-id="{{ $id }}"
>
    <x-mle-media-previews
        :id="$id"
        :model-or-class-name="$modelOrClassName"
        :collections="$collections"
        :single-media="$singleMedia"
        :options="$getOptions()"
        :disabled="$disabled"
        :selectable="$selectable"
        :readonly="$readonly"
        :multiple="$multiple"
        :instance-id="$instanceId"
        :data-source="$dataSource"
        :client-token="$clientToken"
    />
</div>