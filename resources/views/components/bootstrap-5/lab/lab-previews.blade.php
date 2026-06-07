<div class="mle-media-preview-grid" 
     data-mle-media-preview-grid
>
    <x-mle-lab-preview-original 
        :id="$id . '-original'"
        :media="$media"
        :options="$getOptions()"
    />
    <x-mle-lab-preview-base 
        :id="$id . '-base'"
        :media="$media"
        :options="$getOptions()"
    />
</div>