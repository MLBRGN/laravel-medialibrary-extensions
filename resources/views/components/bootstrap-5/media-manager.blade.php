<div
    id="{{ $id }}"
    {{ $attributes->class([
        'mle-component',
        'mle-theme-'.$getConfig('frontendTheme'),
        'mle-media-manager',
        'media-manager-multiple' => $multiple,
        'media-manager-single' => !$multiple,
    ])->merge() }}
    data-mle-media-manager
{{--    data-use-xhr="{{ $getConfig('useXhr') ? 'true' : 'false' }}"--}}
>
    <input id="config-{{ $id }}" type="hidden" class="mle-media-manager-config" data-mle-media-manager-config value='@json($config)'>

    @if(config('media-library-extensions.debug'))
        <div class="mle-component mle-debug-menu">
            <x-mle-shared-debug-button/>
            <x-mle-shared-local-package-icon />
        </div>
    @endif
    
    {{ $component_start ?? '' }}

    <div class="mle-media-manager-layout" data-mle-mle-media-manager-layout>
        {{-- Upload form section --}}
        <div class="mle-media-manager-form {{ $getConfig('showUploadForms') ? '' : 'mle-media-manager-form-hidden' }}">
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
        <div class="mle-media-manager-previews">
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
    for="bootstrap-5|media-manager"
/>