<div class="mle-media-preview-menu">
    <div class="mle-media-preview-menu-start">
        @if($getConfig('showOrder') && $medium->hasCustomProperty('priority'))
            <span class="mle-button-pseudo mle-button-icon-pseudo mle-button-no-border mle-button-no-hover mle-button-transparent"
                title="{{ __('media-library-extensions::messages.set-as-main') }}"
            >
                {{ $medium->getCustomProperty('priority') + 1 }}
            </span>
        @endif

        @if($selectable)
            <label class="mle-button-pseudo mle-button-icon-pseudo mle-checkbox-wrapper">
                <input
                    type="{{ config('media-library-extensions.single_select') ? 'radio' : 'checkbox' }}"
                    name="selected_media"
                    class="mle-media-select-checkbox"
                    data-url="{{ $medium->getUrl() }}"
                    data-alt="{{ $medium->name }}"
                    data-mle-media-select-checkbox
                >
                <span class="mle-media-select-indicator"
                  title="{{ __('media-library-extensions::messages.select') }}"
                />
            </label>
        @endif
    </div>

    <div class="mle-media-preview-menu-end">
        @if($getConfig('showMediaEditButton'))
            @if(isMediaType($medium, 'image') && !isMediaType($medium, 'youtube-video'))
                <button
                    type="button"
                    class="mle-button mle-button-icon"
                    data-mle-modal-trigger="#{{$id}}-iem-{{$medium->id}}"
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
                        class="mle-button mle-button-icon"
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
                :model-or-class-name="$modelOrClassName"
                :medium="$medium"
                :single-medium="$singleMedium"
                :collections="$collections"
                :options="$options"
                :disabled="$disabled"
            />
        @endif
        
    </div>
</div>
