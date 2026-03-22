<div class="mle-media-preview-grid" data-mle-media-preview-grid>
{{--    mpg: {{ $instanceId ?? 'geen' }}--}}
    <x-mle-media-previews
        :id="$id"
        :model-or-class-name="$modelOrClassName"
        :collections="$collections"
        :single-medium="$singleMedium"
        :options="$options"
        :disabled="$disabled"
        :selectable="$selectable"
        :readonly="$readonly"
        :multiple="$multiple"
        :instance-id="$instanceId"
    />
</div>