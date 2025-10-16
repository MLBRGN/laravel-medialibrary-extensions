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
    data-use-xhr="{{ $getOption('useXhr') ? 'true' : 'false' }}"
    >
    <input type="hidden" class="media-manager-config" value='@json($config)'>
    {{ $component_start ?? '' }}
    <div class="media-manager-row row">
        <div @class([
                'media-manager-form',
                'col-12 col-md-4'
            ])>
            @if($getOption('showUploadForm'))
                @if($showRegularUploadForm())
                        <x-mle-partial-upload-form
                            :id="$id"
                            :model-or-class-name="$modelOrClassName"
                            :medium="$medium"
                            :collections="$collections"
                            :options="$options"
{{--                            :temporary-upload-mode="$getOption('temporaryUploadMode')"--}}
                            :temporary-upload-mode="$temporaryUploadMode"
                            :allowed-mime-types="$getOption('allowedMimeTypes')"
                            :upload-to-collection="$getCollectionValue('image' , null)"
                            :show-destroy-button="$getOption('showDestroyButton')"
                            :show-set-as-first-button="$getOption('showSetAsFirstButton')"
                            :show-media-edit-button="$getOption('showMediaEditButton')"
                            :multiple="$multiple"
                            :disabled="$disabled || $disableForm"
                            :readonly="$readonly"
                            :use-xhr="$getOption('useXhr')"
                            :frontend-theme="$getOption('frontendTheme')"
                        />
                @endif
            @endif
            @if($hasCollection('youtube'))
                <x-mle-partial-youtube-upload-form
                    class="mt-3"
                    :id="$id"
                    :model-or-class-name="$modelOrClassName"
                    :medium="$medium"
                    :collections="$collections"
                    :options="$options"
                    :disabled="$disabled || $disableForm"
                    :readonly="$readonly"
                    :multiple="$multiple"
                    :temporary-upload-mode="$temporaryUploadMode"
{{--                    :temporary-upload-mode="$getOption('temporaryUploadMode')"--}}
                    :use-xhr="$getOption('useXhr')"
                    :show-destroy-button="$getOption('showDestroyButton')"
                    :show-set-as-first-button="$getOption('showSetAsFirstButton')"
                    :show-media-edit-button="$getOption('showMediaEditButton')"
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
                    :medium="$medium"
                    :collections="$collections"
                    :options="$options"
                />
            </div>
        </div>
    </div>
    {{ $component_end ?? '' }}

    <x-mle-shared-debug
        :frontend-theme="$frontendTheme"
        :model-or-class-name="$modelOrClassName"
        :config="$config"
        :options="$options"
    />
</div>
<x-mle-shared-assets 
    include-css="true" 
    include-js="true" 
    :frontend-theme="$frontendTheme"
/>
