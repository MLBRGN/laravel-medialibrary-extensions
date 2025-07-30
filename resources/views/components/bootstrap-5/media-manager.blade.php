<div 
    id="{{ $id }}"
    {{ $attributes->class([
        'mlbrgn-mle-component',
        'theme-'.$theme,
        'media-manager',
        'media-manager-multiple' => $multiple,
        'media-manager-single' => !$multiple,
        'container-fluid px-0',
    ])->merge() }}
    data-media-manager=""
    >
    <input type="hidden" class="media-manager-config" value='@json($config)' />
    <x-mle-partial-debug :theme="$theme" :model="$model" :config="$config" :model-type="$modelType" :modelId="$modelId"/>
    <div class="media-manager-row row">
        <div class="media-manager-form col-12 col-md-4">
            @if($uploadEnabled)
                
                <x-mle-partial-upload-form
                    :model-or-class-name="$modelOrClassName"
                    :temporary-upload="$temporaryUpload"
                    :id="$id"
                    :allowed-mime-types="$allowedMimeTypes"
                    :upload-to-collection="$imageCollection"
                    :image-collection="$imageCollection"
                    :document-collection="$documentCollection"
                    :youtube-collection="$youtubeCollection"
                    :destroy-enabled="$destroyEnabled"
                    :set-as-first-enabled="$setAsFirstEnabled"
                    :multiple="$multiple"
                />
            @endif
            @if($youtubeCollection)
                <hr>
                <x-mle-partial-youtube-upload-form
                    class="mt-3"
                    :model-or-class-name="$modelOrClassName"
                    :temporary-upload="$temporaryUpload"
                    :id="$id"
                    :image-collection="$imageCollection"
                    :document-collection="$documentCollection"
                    :youtube-collection="$youtubeCollection"
                    :destroy-enabled="$destroyEnabled"
                    :set-as-first-enabled="$setAsFirstEnabled"
                />
            @endif
            <x-mle-partial-status-area
                id="{{ $id }}"
                :initiator-id="$id"/>
        </div>
        <div class="media-manager-previews col-12 col-md-8">
            <div class="media-manager-preview-grid">
                <x-mle-media-manager-preview
                    :id="$id"
                    :show-order="$showOrder"
                    :destroy-enabled="$destroyEnabled"
                    :set-as-first-enabled="$setAsFirstEnabled"
                    :model="$model"
                    :image-collection="$imageCollection"
                    :youtube-collection="$youtubeCollection"
                    :document-collection="$documentCollection"
                    :temporary-uploads="$temporaryUpload"
                />
            </div>
            {{-- TODO title--}}
            <x-mle-media-modal
                :id="$id"
                :model="$model"
                :media-collections="[$imageCollection, $youtubeCollection, $documentCollection]"
                title="Media carousel"
                :inModal="true"
                :plainJs="false" />
        </div>
    </div>
</div>
<x-mle-partial-assets include-css="true" include-js="true" :frontend-theme="$theme"/>
