<x-mle-shared-local-package-badge/>
<div
    id="{{ $id }}"
    {{ $attributes->class([
        'mlbrgn-mle-component',
        'theme-'.$frontendTheme,
        'media-manager',
        'media-manager-multiple' => $multiple,
        'media-manager-single' => !$multiple,
    ])->merge() }}
    data-media-manager=""
    data-use-xhr="{{ $useXhr ? 'true' : 'false' }}"
    >
    
    <input type="hidden" class="media-manager-config" value='@json($config)'>
    {{ $component_start ?? '' }}
    <div class="media-manager-row row">
        <div class="media-manager-form col-12 col-md-4">
            {{ $form_start ?? '' }}
            @if($showUploadForm)
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
                        :show-destroy-button="$showDestroyButton"
                        :show-set-as-first-button="$showSetAsFirstButton"
                        :show-media-edit-button="$showMediaEditButton"
                        :multiple="$multiple"
                        :disabled="$disabled || $disableForm"
                        :readonly="$readonly"
                        :use-xhr="$useXhr"
                        :frontend-theme="$frontendTheme"
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
                    :show-destroy-button="$showDestroyButton"
                    :show-set-as-first-button="$showSetAsFirstButton"
                    :show-media-edit-button="$showMediaEditButton"
                    :disabled="$disabled || $disableForm"
                    :readonly="$readonly"
                    :multiple="$multiple"
                    :use-xhr="$useXhr"
                />
            @endif
            {{ $form_end ?? '' }}
        </div>
        
        <div class="media-manager-previews">
            <x-mle-partial-status-area
                id="{{ $id }}"
                :initiator-id="$id"
                :media-manager-id="$id"
                :frontend-theme="$frontendTheme"
            />
            <div class="media-manager-preview-grid">
                <x-mle-media-manager-preview
                    :id="$id"
                    :show-order="$showOrder"
                    :show-menu="$showMenu"
                    :show-destroy-button="$showDestroyButton"
                    :show-set-as-first-button="$showSetAsFirstButton"
                    :show-media-edit-button="$showMediaEditButton"
                    :model-or-class-name="$modelOrClassName"
                    :image-collection="$imageCollection"
                    :youtube-collection="$youtubeCollection"
                    :document-collection="$documentCollection"
                    :video-collection="$videoCollection"
                    :audio-collection="$audioCollection"
                    :temporary-uploads="$temporaryUpload"
                    :frontend-theme="$frontendTheme"
                    :use-xhr="$useXhr"
                    :selectable="$selectable"
                    :disabled="$disabled"
                    :readonly="$readonly"
                />
            </div>
        </div>
    </div>
    {{ $component_end ?? '' }}

    <x-mle-shared-debug
        :frontend-theme="$frontendTheme"
        :model-or-class-name="$modelOrClassName"
        :config="$config"
    />
</div>
<x-mle-shared-assets include-css="true" include-js="true" :frontend-theme="$frontendTheme"/>
