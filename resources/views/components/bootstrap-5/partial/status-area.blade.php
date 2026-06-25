<div 
    {{ $attributes->merge(['class' => 'mle-status-area']) }} 
    data-mle-status-area-container 
    id="{{ $getDomId() }}}"
>
    <x-mle-partial-spinner 
        id="{{ $id }}" 
        :initiator-id="$initiatorId"
        :media-manager-dom-id="$mediaManagerDomId"
        :options="$getOptions()"
    />
    <x-mle-partial-status 
        id="{{ $id }}" 
        :initiator-id="$initiatorId"
        :media-manager-dom-id="$mediaManagerDomId"
        :options="$getOptions()"
    />
</div>