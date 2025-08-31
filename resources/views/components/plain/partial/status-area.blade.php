<div class="status-area">
    <x-mle-partial-spinner 
        id="{{ $id.'-alert' }}" 
        :initiator-id="$id"
        :media-manager-id="$id"
        :frontend-theme="$frontendTheme"
    />
    <x-mle-partial-status 
        id="{{ $id.'-alert' }}" 
        :initiator-id="$id"
        :media-manager-id="$id"
        :frontend-theme="$frontendTheme"
    />
</div>