<div 
    id="{{ $id }}"
    {{ $attributes->class([
        'mlbrgn-mle-component',
        'theme-'.$frontendTheme,
        'media-manager',
        'media-manager-multiple' => $multiple,
        'media-manager-single' => !$multiple,
        'container-fluid px-0',
    ])->merge() }}
    data-media-manager=""
    >
    
    <input type="hidden" class="media-manager-config" value='@json($config)' />
    <x-mle-partial-debug 
        :frontend-theme="$frontendTheme" 
        :model="$model" 
        :config="$config" 
        :model-type="$modelType" 
        :modelId="$modelId"
    />
    {{ $component_start ?? '' }}
    <div class="media-manager-row row">
        <div @class([
                'media-manager-form',
                 'col-12 col-md-4' => $uploadEnabled,
                 'col-0' => !$uploadEnabled
            ])>
            {{ $form_start ?? '' }}
            @if($uploadEnabled)
                @if($imageCollection || $documentCollection || $videoCollection || $audioCollection)
                    <x-mle-partial-upload-form
                        :model-or-class-name="$modelOrClassName"
                        :temporary-upload="$temporaryUpload"
                        :id="$id"
                        :allowed-mime-types="$allowedMimeTypes"
                        :upload-to-collection="$imageCollection"
                        :image-collection="$imageCollection"
                        :document-collection="$documentCollection"
                        :youtube-collection="$youtubeCollection"
                        :video-collection="$videoCollection"
                        :audio-collection="$audioCollection"
                        :destroy-enabled="$destroyEnabled"
                        :set-as-first-enabled="$setAsFirstEnabled"
                        :multiple="$multiple"
                        :allowed-mimetypes="$allowedMimeTypes"
{{--                        :disabled="$disableForm"--}}
                    />
                @endif
            @endif
            @if($youtubeCollection)
                <x-mle-partial-youtube-upload-form
                    class="mt-3"
                    :model-or-class-name="$modelOrClassName"
                    :temporary-upload="$temporaryUpload"
                    :id="$id"
                    :image-collection="$imageCollection"
                    :document-collection="$documentCollection"
                    :youtube-collection="$youtubeCollection"
                    :video-collection="$videoCollection"
                    :audio-collection="$audioCollection"
                    :destroy-enabled="$destroyEnabled"
                    :set-as-first-enabled="$setAsFirstEnabled"
{{--                    :disabled="$disableForm"--}}
                    :multiple="$multiple"
                />
            @endif
            {{ $form_end ?? '' }}
        </div>
        <div
            @class([
                'media-manager-previews',
                 'col-12 col-md-8' => $uploadEnabled,
                 'col-12' => !$uploadEnabled
            ])
            class="media-manager-previews col-12 col-md-8">
            <x-mle-partial-status-area
                id="{{ $id }}"
                :initiator-id="$id"/>
            <div class="media-manager-preview-grid">
                <x-mle-media-manager-preview
                    :id="$id"
                    :show-order="$showOrder"
                    :show-menu="$showMenu"
                    :destroy-enabled="$destroyEnabled"
                    :set-as-first-enabled="$setAsFirstEnabled"
                    :model-or-class-name="$modelOrClassName"
                    :image-collection="$imageCollection"
                    :youtube-collection="$youtubeCollection"
                    :document-collection="$documentCollection"
                    :video-collection="$videoCollection"
                    :audio-collection="$audioCollection"
                    :temporary-uploads="$temporaryUpload"
                    :frontend-theme="$frontendTheme"
                />
            </div>
            {{-- TODO title--}}
            <x-mle-media-modal
                :id="$id"
                :model-or-class-name="$modelOrClassName"
                :media-collections="[$imageCollection, $youtubeCollection, $documentCollection, $videoCollection, $audioCollection]"
                title="Media carousel"
                :inModal="true"
                :plainJs="false" />
        </div>
    </div>
    {{ $component_end ?? '' }}
</div>
<x-mle-partial-assets include-css="true" include-js="true" :frontend-theme="$frontendTheme"/>
