<x-mle-shared-local-package-badge/>
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
    data-use-xhr="{{ $useXhr ? 'true' : 'false' }}"
    >
    <input type="hidden" class="media-manager-config" value='@json($config)'>
    {{ $component_start ?? '' }}
    <div class="media-manager-row row">
        <div @class([
                'media-manager-form',
                'col-12 col-md-4'
            ])>
            @if($showUploadForm)
                @if($showRegularUploadForm())
                        <x-mle-partial-upload-form
                            :model-or-class-name="$modelOrClassName"
                            :medium="$medium"
                            :temporary-upload-mode="$temporaryUploadMode"
                            :id="$id"
                            :options="$options"
                            :allowed-mime-types="$allowedMimeTypes"
                            :upload-to-collection="$getCollectionValue('image' , null)"
                            :collections="$collections"
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
            @if($hasCollection('youtube'))
                <x-mle-partial-youtube-upload-form
                    class="mt-3"
                    :model-or-class-name="$modelOrClassName"
                    :medium="$medium"
                    :options="$options"
                    :temporary-upload-mode="$temporaryUploadMode"
                    :id="$id"
                    :collections="$collections"
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
        <div
            @class([
                'media-manager-previews',
                'col-12 col-md-8'
            ])
            >
            <x-mle-partial-status-area
                id="{{ $id }}"
                :initiator-id="$id"
                :media-manager-id="$id"
                :frontend-theme="$frontendTheme"
            />
            <div class="media-manager-preview-grid">
                <x-mle-media-manager-preview
                    :id="$id"
                    :model-or-class-name="$modelOrClassName"
                    :options="$options"
                    :medium="$medium"
                    :collections="$collections"
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
