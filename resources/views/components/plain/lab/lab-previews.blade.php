<div class="mle-media-preview-grid" 
     data-mle-media-preview-grid
     id="{{ $getDomId() }}"
>
    <x-mle-lab-preview-original 
        :id="$id"
        :media="$media"
        :options="$getOptions()"
    />
    <x-mle-lab-preview-base 
        :id="$id"
        :media="$media"
        :options="$getOptions()"
    />
</div>