<div {{ $attributes->merge(['class' => 'mle-status-area']) }} data-mle-status-area-container>
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