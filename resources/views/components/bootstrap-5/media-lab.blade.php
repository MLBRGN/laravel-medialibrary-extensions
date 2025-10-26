<div class="mlbrgn-mle-component theme-bootstrap-5 mle-media-lab" data-media-manager-lab>
    <input type="hidden" class="media-manager-config" data-media-manager-config value='@json($config)'>
   
    <div class="media-manager-lab-previews" data-media-manager-lab-previews>
        <x-mle-media-lab-previews
            :medium="$medium"
        />
    </div>
</div>

<x-mle-shared-assets
    include-css="true"
    include-js="true"
    include-media-manager-lab-submitter="true"
    :frontend-theme="$getConfig('frontendTheme')"
/>