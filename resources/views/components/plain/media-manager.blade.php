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
    <input id="config-{{ $id }}" type="hidden" class="media-manager-config" data-media-manager-config value='@json($config)'>

    {{ $component_start ?? '' }}

    <div class="media-manager-layout" data-media-manager-layout>
        {{-- Upload form section --}}
        <div class="media-manager-form {{ $getConfig('showUploadForms') ? '' : 'media-manager-forms-hidden' }}">
            @if($getConfig('showUploadForms'))
                {{ $form_start ?? '' }}
                @if($getConfig('showUploadForm'))
                    <x-mle-partial-upload-form
                        :id="$id"
                        :model-or-class-name="$modelOrClassName"
                        :single-medium="$singleMedium"
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
                        :single-medium="$singleMedium"
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

        {{-- Preview section --}}
        <div class="media-manager-previews">
            <x-mle-partial-status-area
                id="{{ $id }}"
                :initiator-id="$id"
                :media-manager-id="$id"
                :options="$options"
            
            />

            <x-mle-media-preview-grid
                :id="$id"
                :model-or-class-name="$modelOrClassName"
                :single-medium="$singleMedium"
                :collections="$collections"
                :options="$options"
                :selectable="$selectable"
                :disabled="$disabled"
                :readonly="$readonly"
                :multiple="$multiple"
            />
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