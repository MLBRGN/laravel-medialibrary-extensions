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
>
    <input id="config-{{ $id }}" type="hidden" class="mle-media-manager-config" data-mle-media-manager-config value='@json($getConfig())'>

    @if(config('medialibrary-extensions.debug'))
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
{{--                TODO when single show 1, otherwise max from config--}}
                <span class="mle-media-manager-media-counts">{{ $totalMediaCount }} / {{ $maxMediaCount }}</span>
                @if($totalMediaCount >= $maxMediaCount)
                    <div class="mle-alert alert alert-primary">
                        @if(!$multiple)
                        {{ __('medialibrary-extensions::messages.upload_disabled_only_one_medium_allowed') }}
                        @elseif($multiple)
                        {{ __('medialibrary-extensions::messages.upload_disabled_max_items_reached') }}
                        @endif
                    </div>
                @else
                    @if($getConfig('disableForm'))
                        <div class="mle-alert alert alert-primary">
                            {{ __('medialibrary-extensions::messages.disabled') }}
                        </div>
                    @endif
                @endif
                {{ $form_start ?? '' }}
                @if($getConfig('showUploadForm'))
                    <x-mle-partial-upload-form
                        :id="$id"
                        :media-manager-id="$mediaManagerId"
                        :model-or-class-name="$modelOrClassName"
                        :single-media="$singleMedia"
                        :collections="$collections"
                        :options="$getOptions()"
                        :multiple="$multiple"
                        :disabled="$disabled || $getConfig('disableForm')"
                        :readonly="$readonly"
                        :instance-id="$getConfig('instanceId')"
                        :data-source="$getConfig('dataSource')"
                    />
                @endif

                @if($getConfig('showYouTubeUploadForm'))
                <x-mle-partial-youtube-upload-form
                        class="mt-3"
                        :id="$id"
                        :media-manager-id="$mediaManagerId"
                        :model-or-class-name="$modelOrClassName"
                        :single-media="$singleMedia"
                        :collections="$collections"
                        :options="$getOptions()"
                        :disabled="$disabled || $getConfig('disableForm')"
                        :readonly="$readonly"
                        :multiple="$multiple"
                        :instance-id="$getConfig('instanceId')"
                        :data-source="$getConfig('dataSource')"
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
                :media-manager-id="$mediaManagerId"
                :options="$getOptions()"
                :instance-id="$getConfig('instanceId')"
            />

            <x-mle-media-preview-grid
                :id="$id"
                :media-manager-id="$mediaManagerId"
                :model-or-class-name="$modelOrClassName"
                :single-media="$singleMedia"
                :collections="$collections"
                :options="$getOptions()"
                :selectable="$selectable"
                :disabled="$disabled"
                :readonly="$readonly"
                :multiple="$multiple"
                :instance-id="$getConfig('instanceId')"
                :data-source="$getConfig('dataSource')"
            />
        </div>
    </div>

    {{ $component_end ?? '' }}

    <x-mle-shared-debug
        :model-or-class-name="$modelOrClassName"
        :config="$getConfig()"
        :options="$getOptions()"
        :data-source="$dataSource"
    />
</div>
    <x-mle-shared-assets
        include-css="true"
        include-js="true"
        include-debug-toggle-js="{{ config('medialibrary-extensions.debug') }}"
        :frontend-theme="$getConfig('frontendTheme')"
        for="bootstrap-5|media-manager"
    />