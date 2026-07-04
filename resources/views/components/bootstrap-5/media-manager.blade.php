<div
    id="{{ $getDomId() }}"
    data-base-id="{{ $id }}"
    {{ $attributes->class([
        'mle-component',
        'mle-theme-'.$getConfig('theme'),
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
                <span class="mle-media-manager-media-counts">{{ $totalMediaCount }} / {{ $maxMediaCount }}</span>
                @if($totalMediaCount >= $maxMediaCount)
                    <div class="mle-alert alert alert-primary" data-mle-max-reached-alert>
                        @if(!$multiple)
                        {{ __('medialibrary-extensions::messages.upload_disabled_only_one_medium_allowed') }}
                        @elseif($multiple)
                        {{ __('medialibrary-extensions::messages.upload_disabled_max_items_reached') }}
                        @endif
                    </div>
                @else
                    @if($getConfig('disableForm'))
                        <div class="mle-alert alert alert-primary" data-mle-disabled-alert>
                            {{ __('medialibrary-extensions::messages.disabled') }}
                        </div>
                    @endif
                @endif
                {{ $form_start ?? '' }}
            
                @if($getConfig('showUploadForm'))
                    <x-mle-partial-upload-form
                        :id="$id"
                        :model-or-class-name="$modelOrClassName"
                        :single-media="$singleMedia"
                        :collections="$collections"
                        :options="$getOptions()"
                        :multiple="$multiple"
                        :disabled="$disabled || $getConfig('disableForm')"
                        :readonly="$readonly"
                        :instance-id="$instanceId"
                        :data-source="$dataSource"
                        :client-token="$clientToken"
                    />
                @endif

                @if($getConfig('showYouTubeUploadForm'))
                <x-mle-partial-youtube-upload-form
                        class="mt-3"
                        :id="$id"
                        :model-or-class-name="$modelOrClassName"
                        :single-media="$singleMedia"
                        :collections="$collections"
                        :options="$getOptions()"
                        :disabled="$disabled || $getConfig('disableForm')"
                        :readonly="$readonly"
                        :multiple="$multiple"
                        :instance-id="$instanceId"
                        :data-source="$dataSource"
                        :client-token="$clientToken"
                    />
                @endif
            @endif
            {{ $form_end ?? '' }}
        </div>

        {{-- Preview section --}}
        <div class="mle-media-manager-previews">
            <x-mle-partial-status-area
                :id="$id"
                :options="$getOptions()"
                :instance-id="$instanceId"
            />

            <x-mle-media-preview-grid
                :id="$id"
                :model-or-class-name="$modelOrClassName"
                :single-media="$singleMedia"
                :collections="$collections"
                :options="$getOptions()"
                :selectable="$selectable"
                :disabled="$disabled"
                :readonly="$readonly"
                :multiple="$multiple"
                :instance-id="$instanceId"
                :data-source="$dataSource"
                :client-token="$clientToken"
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
        :frontend-theme="$getConfig('theme')"
        for="bootstrap-5|media-manager"
    />