<x-mle-shared-local-package-badge/>
<div
    id="{{ $id }}"
    {{ $attributes->class([
        'mlbrgn-mle-component',
        'theme-'.$getConfig('frontendTheme'),
        'media-manager',
        'media-manager-multiple' => $multiple,
        'media-manager-single' => !$multiple,
        'container-fluid px-0',
    ])->merge() }}
    data-media-manager=""
    data-use-xhr="{{ $getConfig('useXhr') ? 'true' : 'false' }}"
    >
    <input type="hidden" class="media-manager-config" value='@json($config)'>
    {{ $component_start ?? '' }}
    <div class="media-manager-row row">
        <div @class([
                'media-manager-form',
                'col-12 col-md-4' => $getConfig('showUploadForms') === true,
                'col-0' => $getConfig('showUploadForms') === false,
            ])>
            @if($getConfig('showUploadForms'))
                @if($getConfig('showUploadForm'))
                    <x-mle-partial-upload-form
                        :id="$id"
                        :model-or-class-name="$modelOrClassName"
                        :medium="$medium"
                        :collections="$collections"
                        :options="$options"
                        :multiple="$multiple"
                        :disabled="$disabled || $getConfig('disableForm')"
                        :readonly="$readonly"
                    />
                @endif
                @if($getConfig('showYouTubeUploadForm'))
                    <x-mle-partial-youtube-upload-form
                        class="mt-3"
                        :id="$id"
                        :model-or-class-name="$modelOrClassName"
                        :medium="$medium"
                        :collections="$collections"
                        :options="$options"
                        :disabled="$disabled || $getConfig('disableForm')"
                        :readonly="$readonly"
                        :multiple="$multiple"
                    />
                @endif
            @endif
            {{ $form_end ?? '' }}
        </div>
        <div
            @class([
                'media-manager-previews',
                'col-12 col-md-8' => $getConfig('showUploadForms') === true,
                'col-12' => $getConfig('showUploadForms') === false,
            ])
            >
            <x-mle-partial-status-area
                id="{{ $id }}"
                :initiator-id="$id"
                :media-manager-id="$id"
                :options="$options"
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
        :model-or-class-name="$modelOrClassName"
        :config="$config"
        :options="$options"
    />
</div>
<x-mle-shared-assets 
    include-css="true" 
    include-js="true" 
    :frontend-theme="$getConfig('frontendTheme')"
/>
