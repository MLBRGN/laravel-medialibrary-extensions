<div id="{{ $domId }}"
    @class([
        'mle-component',
        'mle-theme-'.$getConfig('frontendTheme'), 
        'mle-media-lab'
    ])
    data-mle-media-manager-lab
>
    <input id="config-{{ $id }}" type="hidden" class="mle-media-manager-config" data-mle-media-manager-config value='@json($getConfig())'>

    <x-mle-partial-status-area
        id="{{ $domId }}"
        :initiator-id="$id"
        :media-manager-dom-id="$id"
    />
    <div class="mle-media-manager-lab-previews" data-mle-media-manager-lab-previews>
        <x-mle-lab-previews
            :media="$media"
            :options="$getOptions()"
        />
    </div>
</div>
<x-mle-shared-assets
    include-css="true"
    include-js="true"
    include-media-manager-lab-submitter="true"
    include-debug-toggle-js="{{ config('medialibrary-extensions.debug') }}"
    :frontend-theme="$getConfig('frontendTheme')"
    for="bootstrap-5|media-lab"
/>