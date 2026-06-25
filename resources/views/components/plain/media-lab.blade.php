<div id="{{ $getDomId() }}"
    @class([
        'mle-component',
        'mle-theme-'.$getConfig('frontendTheme'), 
        'mle-media-lab'
    ])
    data-mle-media-manager-lab
>
    <input id="config-{{ $id }}" type="hidden" class="mle-media-manager-config" data-mle-media-manager-config value='@json($getConfig())'>

    <x-mle-partial-status-area
        id="{{ $id }}"
        :initiator-id="$id"
        :media-manager-dom-id="$id"
    />
    <div class="mle-media-manager-lab-previews" data-mle-media-manager-lab-previews>
        <x-mle-lab-previews
            :media="$media"
            :options="$getOptions()"
        />
    </div>

    @if(config('medialibrary-extensions.debug'))
        <div class="mle-component mle-debug-menu">
            <x-mle-shared-debug-button/>
            <x-mle-shared-local-package-icon />
        </div>
    @endif

    <x-mle-shared-debug
        :model-or-class-name="$modelOrClassName"
        :config="$getConfig()"
        :options="$getOptions()"
        data-source="default"
    />
</div>
<x-mle-shared-assets
    include-css="true"
    include-js="true"
    include-media-manager-lab-submitter="true"
    include-debug-toggle-js="{{ config('medialibrary-extensions.debug') }}"
    :frontend-theme="$getConfig('frontendTheme')"
    for="plain|media-lab"
/>