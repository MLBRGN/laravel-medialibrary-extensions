<div {{ $attributes->merge(['class' => 'mle-status-area']) }} data-mle-status-area-container>
    <x-mle-partial-spinner 
        id="{{ $domId.'-spinner' }}" 
        :initiator-id="$initiatorId"
        :media-manager-id="$mediaManagerId"
        :options="$getOptions()"
    />
    <x-mle-partial-status 
        id="{{ $domId.'-alert' }}" 
        :initiator-id="$initiatorId"
        :media-manager-id="$mediaManagerId"
        :options="$getOptions()"
    />
</div>