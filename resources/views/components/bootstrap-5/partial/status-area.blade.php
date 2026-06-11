<div {{ $attributes->merge(['class' => 'mle-status-area']) }} data-mle-status-area-container data-test="status-area-{{ $id }}">
    <x-mle-partial-spinner 
        id="{{ $id.'-alert' }}" 
        :initiator-id="$id"
        :media-manager-id="$id"
        :options="$getOptions()"
    />
    <x-mle-partial-status 
        id="{{ $id.'-alert' }}" 
        :initiator-id="$id"
        :media-manager-id="$id"
        :options="$getOptions()"
    />
</div>