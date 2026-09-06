<div id="{{ $getDomId() }}"
    @class([
        'mle-component',
        'mle-theme-'.$getConfig('theme'), 
        'mle-media-lab'
    ])
    data-mle-media-lab
>
    <input id="config-{{ $id }}" type="hidden" class="mle-media-manager-config" data-mle-media-manager-config value='@json($getConfig())'>
    <input type="hidden" name="client_token" value="{{ $clientToken }}" data-mle-client-token>

    <x-mle-partial-status-area
        id="{{ $id }}"
    />
    <div class="mle-media-lab-previews" data-mle-media-lab-previews>
        <x-mle-lab-previews
            :id="$id"
            :media="$media"
            :options="$getOptions()"
            :data-source="$dataSource"
        />
    </div>
</div>
<x-mle-shared-assets
    :include-css="true"
    :include-js="true"
    :include-media-lab-submitter="true"
    :include-debug-toggle-js="config('medialibrary-extensions.debug')"
    :theme="$getConfig('theme')"
    for="plain|media-lab"
/>