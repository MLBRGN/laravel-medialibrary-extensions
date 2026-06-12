<div {{ $attributes->merge(['class' => 'mle-status-area']) }} data-mle-status-area-container data-test="status-area-{{ $id }}">
    <x-mle-partial-spinner 
        id="{{ $id.'-spinner' }}" 
        :initiator-id="$initiatorId"
        :media-manager-id="$mediaManagerId"
        :options="$getOptions()"
    />
    <x-mle-partial-status 
        id="{{ $id.'-alert' }}" 
        :initiator-id="$initiatorId"
        :media-manager-id="$mediaManagerId"
        :options="$getOptions()"
    />
</div>