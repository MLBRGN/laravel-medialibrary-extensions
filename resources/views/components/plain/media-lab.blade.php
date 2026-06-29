<div id="{{ $getDomId() }}"
    @class([
        'mle-component',
        'mle-theme-'.$getConfig('frontendTheme'), 
        'mle-media-lab'
    ])
    data-mle-media-lab
>
    <input id="config-{{ $id }}" type="hidden" class="mle-media-manager-config" data-mle-media-manager-config value='@json($getConfig())'>

    <x-mle-partial-status-area
        id="{{ $id }}"
    />
    <div class="mle-media-manager-lab-previews" data-mle-media-lab-previews>
        <x-mle-lab-previews
            :id="$id"
            :media="$media"
            :options="$getOptions()"
            :data-source="$dataSource"
        />
    </div>
</div>
<x-mle-shared-assets
    include-css="true"
    include-js="true"
    include-media-manager-lab-submitter="true"
    include-debug-toggle-js="{{ config('medialibrary-extensions.debug') }}"
    :frontend-theme="$getConfig('frontendTheme')"
    for="plain|media-lab"
/>