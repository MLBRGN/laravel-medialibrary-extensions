<div {{ $attributes->merge(['class' => 'status-area']) }} data-status-area-container>
    <x-mle-partial-spinner 
        id="{{ $id.'-alert' }}" 
        :initiator-id="$id"
        :media-manager-id="$id"
        :options="$options"
    />
    <x-mle-partial-status 
        id="{{ $id.'-alert' }}" 
        :initiator-id="$id"
        :media-manager-id="$id"
        :options="$options"
    />
</div>