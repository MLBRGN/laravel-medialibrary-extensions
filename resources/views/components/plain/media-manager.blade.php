<x-mle-shared-local-package-badge/>
<div
    id="{{ $id }}"
    {{ $attributes->class([
        'mlbrgn-mle-component',
        'theme-'.$getConfig('frontendTheme'),
        'media-manager',
        'media-manager-multiple' => $multiple,
        'media-manager-single' => !$multiple,
    ])->merge() }}
    data-media-manager=""
    data-use-xhr="{{ $getConfig('useXhr') ? 'true' : 'false' }}"
    >
    <input type="hidden" class="media-manager-config" value='@json($config)'>
    {{ $component_start ?? '' }}
    <div class="media-manager-row row">
        <div @class([
                'media-manager-form',
            ])>
            {{ $form_start ?? '' }}
            @if($getConfig('showUploadForm'))
{{--                @if($showRegularUploadForm())--}}
                    <x-mle-partial-upload-form
                        :id="$id"
                        :model-or-class-name="$modelOrClassName"
                        :medium="$medium"
                        :collections="$collections"
                        :options="$options"
                        :multiple="$multiple"
{{--                        :upload-to-collection="$getCollectionValue('image' , null)"--}}
{{--                        :disabled="$disabled || $disableForm"--}}
{{--                        :readonly="$readonly"--}}
                    />
{{--                @endif--}}
            @endif
            @if($hasCollection('youtube'))
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
{{--                    :temporary-upload-mode="$temporaryUploadMode"--}}
{{--                    :temporary-upload-mode="$getConfig('temporaryUploadMode')"--}}
{{--                    :use-xhr="$useXhr"--}}
{{--                    :show-destroy-button="$showDestroyButton"--}}
{{--                    :show-set-as-first-button="$showSetAsFirstButton"--}}
{{--                    :show-media-edit-button="$showMediaEditButton"--}}
                />
            @endif
            {{ $form_end ?? '' }}
        </div>
        <div
            @class([
                'media-manager-previews',
            ])
            >
            <x-mle-partial-status-area
                id="{{ $id }}"
                :initiator-id="$id"
                :media-manager-id="$id"
                :frontend-theme="$getConfig('frontendTheme')"
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
