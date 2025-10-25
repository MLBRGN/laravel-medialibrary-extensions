<div class="media-manager-preview-menu">
    <div class="media-manager-preview-image-menu-start">
        @if($getConfig('showOrder') && $medium->hasCustomProperty('priority'))
            <span class="mle-pseudo-button mle-pseudo-button-icon"
                title="{{ __('media-library-extensions::messages.set-as-main') }}"
            >
                {{ $medium->getCustomProperty('priority') + 1 }}
            </span>
        @endif

        @if($selectable)
            <label class="mle-pseudo-button mle-pseudo-button-icon mle-checkbox-wrapper">
                <input
                    type="{{ config('media-library-extensions.single_select') ? 'radio' : 'checkbox' }}"
                    name="selected_media"
                    class="mle-media-select-checkbox"
                    data-url="{{ $medium->getUrl() }}"
                    data-alt="{{ $medium->name }}"
                >
                <span class="mle-media-select-indicator"
                  title="{{ __('media-library-extensions::messages.select') }}"
                />
            </label>
        @endif
    </div>

    <div class="media-manager-preview-image-menu-end">
        @if($getConfig('showMediaEditButton'))
            @if(isMediaType($medium, 'image') && !isMediaType($medium, 'youtube-video'))
                <button
                    type="button"
                    class="mle-button mle-button-icon btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#{{ $id }}-iem-{{ $medium->id }}"
                    title="{{ __('media-library-extensions::messages.edit') }}"
                    @disabled($disabled)
                >
                    <x-mle-shared-icon
                        name="{{ config('media-library-extensions.icons.edit') }}"
                        title="{{ __('media-library-extensions::messages.edit') }}"
                    />
                </button>
            @endif
        @endif

        @if($getConfig('showSetAsFirstButton'))
            @if($medium->getCustomProperty('priority') === 0)
                <button type="button"
                        class="mle-button mle-button-icon btn btn-primary"
                        title="{{ __('media-library-extensions::messages.set-as-main') }}"
                        disabled
                >
                    <x-mle-shared-icon
                        name="{{ config('media-library-extensions.icons.set-as-main') }}"
                        title="{{ __('media-library-extensions::messages.medium_set_as_main') }}"
                    />
                </button>
            @else
                <x-mle-partial-set-as-first-form
                    :id="$id"
                    :model-or-class-name="$modelOrClassName"
                    :medium="$medium"
                    :single-medium="$singleMedium"
                    :collections="$collections"
                    :options="$options"
                    :disabled="$disabled"
                />
            @endif
        @endif

        @if($getConfig('showDestroyButton'))
            <x-mle-partial-destroy-form
                :id="$id"
                :medium="$medium"
                :single-medium="$singleMedium"
                :collections="$collections"
                :options="$options"
                :disabled="$disabled"
            />
        @endif
        
    </div>
</div>
