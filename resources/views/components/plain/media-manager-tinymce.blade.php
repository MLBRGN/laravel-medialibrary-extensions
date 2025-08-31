<div class="media-manager-wrapper">
    @if($temporaryUpload)
        <x-mle-media-manager
            :model-or-class-name="$modelType"
            id="{{ $id }}"
            :image-collection="$imageCollection"
            :document-collection="$documentCollection"
            :youtube-collection="$youtubeCollection"
            :video-collection="$videoCollection"
            :audio-collection="$audioCollection"
            :frontend-theme="'default'"
            :destroy-enabled="true"
            :set-as-first-enabled="true"
            :show-order="true"
            :show-menu="true"
        />
    @else
        <x-mle-media-manager
            :model-or-class-name="$model"
            id="{{ $id }}"
            :image-collection="$imageCollection"
            :document-collection="$documentCollection"
            :youtube-collection="$youtubeCollection"
            :video-collection="$videoCollection"
            :audio-collection="$audioCollection"
            :frontend-theme="'default'"
            :destroy-enabled="true"
            :set-as-first-enabled="true"
            :show-order="true"
            :show-menu="true"
        />
    @endif
</div>
