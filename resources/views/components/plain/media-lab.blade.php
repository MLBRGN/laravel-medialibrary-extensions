<div id="{{ $id }}"
    @class([
        'mle-component',
        'mle-theme-'.$getConfig('frontendTheme'), 
        'mle-media-lab'
    ])
    data-mle-media-manager-lab
>
    <input id="config-{{ $id }}" type="hidden" class="mle-media-manager-config" data-mle-media-manager-config value='@json($config)'>

    <x-mle-partial-status-area
        id="{{ $id }}"
        :initiator-id="$id"
        :media-manager-id="$id"
    />
    <div class="mle-media-manager-lab-previews" data-mle-media-manager-lab-previews>
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
    for="plain|media-lab"
/>