<div class="mle-media-preview-menu" data-test="media-preview-menu">
    <div class="mle-media-preview-menu-start">
        @if($getConfig('showOrder') && $medium->hasCustomProperty('priority'))
            <span class="mle-button-pseudo mle-button-icon-pseudo mle-button-no-border mle-button-no-hover mle-button-transparent">
                {{ $medium->getCustomProperty('priority') + 1 }}
            </span>
        @endif

        @if($selectable)
            <label class="mle-button-pseudo mle-button-icon-pseudo mle-checkbox-wrapper">
                <input
                    type="{{ config('medialibrary-extensions.single_select') ? 'radio' : 'checkbox' }}"
                    name="selected_media"
                    class="mle-media-select-checkbox"
                    data-url="{{ $medium->getUrl() }}"
                    data-alt="{{ $medium->name }}"
                    data-mle-media-select-checkbox
                    data-test="media-select"
                >
                <span class="mle-media-select-indicator"
                  title="{{ __('medialibrary-extensions::messages.select') }}"
                />
            </label>
        @endif
    </div>

    <div class="mle-media-preview-menu-end" data-test="media-preview-menu-end-{{ $id }}" data-test-id="{{ $id }}">
        @if($getConfig('showMediaEditButton'))
            @if(isMediaType($medium, 'image') && !isMediaType($medium, 'youtube-video'))
                <button
                    type="button"
                    class="mle-button mle-button-icon btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#{{ $id }}-iem-{{ $medium->id }}"
                    title="{{ __('medialibrary-extensions::messages.edit') }}"
                    data-test="media-edit-button"
                    data-test-id="{{ $id }}"
                    @disabled($disabled)
                >
                    <x-mle-shared-icon
                        name="{{ config('medialibrary-extensions.icons.edit') }}"
                        title="{{ __('medialibrary-extensions::messages.edit') }}"
                    />
                </button>
            @endif
        @endif

        @if($getConfig('showSetAsFirstButton'))
            @if($medium->getCustomProperty('priority') === 0)
                <button type="button"
                        class="mle-button mle-button-icon btn btn-primary"
                        title="{{ __('medialibrary-extensions::messages.set-as-main') }}"
                        data-test-id="{{ $id }}"
                        data-test="media-set-as-first-button"
                        disabled
                >
                    <x-mle-shared-icon
                        name="{{ config('medialibrary-extensions.icons.set-as-main') }}"
                        title="{{ __('medialibrary-extensions::messages.medium_set_as_main') }}"
                    />
                </button>
            @else
                <x-mle-partial-set-as-first-form
                    :id="$id"
                    :media-manager-id="$mediaManagerId"
                    :model-or-class-name="$modelOrClassName"
                    :medium="$medium"
                    :single-media="$singleMedia"
                    :collections="$collections"
                    :options="$getOptions()"
                    :disabled="$disabled"
                    :data-source="$getConfig('dataSource')"
                />
            @endif
        @endif

        @if($getConfig('showDestroyButton'))
            <x-mle-partial-destroy-form
                :id="$id"
                :media-manager-id="$mediaManagerId"
                :model-or-class-name="$modelOrClassName"
                :medium="$medium"
                :single-media="$singleMedia"
                :collections="$collections"
                :options="$getOptions()"
                :disabled="$disabled"
                :data-source="$getConfig('dataSource')"
            />
        @endif
        
    </div>
</div>
