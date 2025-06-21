<div
    id="{{ $id }}"
    {{ $attributes->class([
        'mlbrgn-mle-component',
        'media-manager',
        'media-manager-multiple' => $multiple,
        'media-manager-single' => !$multiple,
    ]) }}
    data-media-manager=""
    >
    <input type="hidden" class="media-manager-config" value='@json($config)' />
    <x-mle-partial-debug :theme="$theme" :model="$model"/>
    <div class="media-manager-row">
        <div class="media-manager-form">
            @if($uploadEnabled)
                <x-mle-partial-upload-form
                    :allowed-mime-types="$allowedMimeTypes"
                    :upload-to-collection="$imageCollection"
                    :image-collection="$imageCollection"
                    :document-collection="$documentCollection"
                    :youtube-collection="$youtubeCollection"
                    :destroy-enabled="$destroyEnabled"
                    :set-as-first-enabled="$setAsFirstEnabled"
                    :model="$model" 
                    :id="$id"
                    :multiple="$multiple"
                />
            @endif
            @if($youtubeCollection)
                <hr>
                <x-mle-partial-youtube-upload-form
                    class="mt-3"
                    :model="$model"
                    :id="$id"
                    :image-collection="$imageCollection"
                    :document-collection="$documentCollection"
                    :youtube-collection="$youtubeCollection"
                    :model="$model"
                    :id="$id"
                    :destroy-enabled="$destroyEnabled"
                    :set-as-first-enabled="$setAsFirstEnabled"
                    :model="$model"
                />
            @endif
            <x-mle-partial-status-area
                id="{{ $id }}"
                :initiator-id="$id"/>
        </div>
        <div class="media-manager-previews">
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
                />
            </div>
            {{-- TODO title--}}
            <x-mle-media-modal
                :id="$id"
                :model="$model"
                :media-collections="[$imageCollection, $youtubeCollection, $documentCollection]"
                title="Media carousel"
                :inModal="true"
                :plainJs="true" />
        </div>
    </div>
</div>
<x-mle-partial-assets include-css="true" include-js="true"/>
