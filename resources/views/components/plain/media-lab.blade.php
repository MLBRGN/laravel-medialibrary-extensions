<div id="{{ $id }}"
    @class([
        'mlbrgn-mle-component',
        'theme-'.$getConfig('frontendTheme'), 
        'mle-media-lab'
    ])
     data-media-manager-lab
>
    <input id="config-{{ $id }}" type="hidden" class="media-manager-config" data-media-manager-config value='@json($config)'>

    <x-mle-partial-status-area
        id="{{ $id }}"
        :initiator-id="$id"
        :media-manager-id="$id"
    />
    <div class="media-manager-lab-previews" data-media-manager-lab-previews>
        <x-mle-lab-previews
            :medium="$medium"
            :options="$options"
        />
    </div>
</div>
<x-mle-shared-assets
    include-css="true"
    include-js="true"
    include-media-manager-lab-submitter="true"
    :frontend-theme="$getConfig('frontendTheme')"
/>