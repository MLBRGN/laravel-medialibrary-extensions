@forelse ($media as $medium)
    <div
        {{ $attributes->class([
            'mlbrgn-mle-component',
             'theme-'.$getConfig('frontendTheme'),
             'media-manager-preview-media-container'
        ]) }}
        data-set-as-first-route="{{ route(mle_prefix_route('set-as-first'), $medium) }}"
        data-destroy-route="{{ route(mle_prefix_route('medium-destroy'), $medium) }}"
    >
        @if(isMediaType($medium, 'youtube-video'))
            <div
                class="media-manager-preview-item-container"
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-mod"
            >
                <x-mle-video-youtube
                    class="mle-video-youtube mle-video-responsive mle-cursor-zoom-in"
                    data-bs-target="#{{$id}}-mod-crs"
                    data-bs-slide-to="{{ $loop->index }}"
                    :medium="$medium"
                    :preview="true"
                    :options="$options"
                    :frontend-theme="$frontendTheme"
                />
            </div>
        @elseif(isMediaType($medium, 'document'))
            <div
                class="media-manager-preview-item-container"
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-mod"
            >
                <x-mle-document
                    class="previewed-document mle-cursor-zoom-in"
                    data-bs-target="#{{ $id }}-mod-crs"
                    data-bs-slide-to="{{ $loop->index }}"
                    :medium="$medium"
                    :options="$options"
                />
            </div>
        @elseif(isMediaType($medium, 'video'))
            <div
                class="media-manager-preview-item-container"
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-mod"
            >
                <x-mle-video
                    class="mle-cursor-zoom-in"
                    data-bs-target="#{{ $id }}-mod-crs"
                    data-bs-slide-to="{{ $loop->index }}"
                    :medium="$medium" 
                    :options="$options"
                />
            </div>
        @elseif(isMediaType($medium, 'audio'))
            <div
                class="media-manager-preview-item-container"
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-mod"
            >
                <x-mle-audio
                    class="mle-cursor-zoom-in"
                    data-bs-target="#{{ $id }}-mod-crs"
                    data-bs-slide-to="{{ $loop->index }}"
                    :medium="$medium" 
                    :options="$options"
                />
            </div>
        @elseif(isMediaType($medium, 'image'))
            <div
                class="media-manager-preview-item-container"
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-mod"
            >
{{--                TODO look at this. Needs options?--}}
                <x-mle-image-responsive
                    class="media-manager-image-preview mle-cursor-zoom-in"
                    data-bs-target="#{{$id}}-mod-crs"
                    data-bs-slide-to="{{ $loop->index }}"
                    :medium="$medium"
                    :options="$options"
                    draggable="false"
                />
            </div>
            <x-mle-image-editor-modal
                id="{{ $id }}"
                :model-or-class-name="$modelOrClassName"
                :medium="$medium"
                :collections="$collections"
                :options="$options"
                :initiator-id="$id"
                :disabled="$disabled"
                :options="$options"
                title="TODO"
                :frontend-theme="$getConfig('frontendTheme')"
                :use-xhr="$getConfig('useXhr')"
            />
        @else
            {{ __('media-library-extensions::messages.non_supported_file_format') }}
        @endif
        @if($getConfig('showMenu'))
            <div class="media-manager-preview-menu">
                <div class="media-manager-preview-image-menu-start">
                    @if($getConfig('showOrder'))
                        @if($medium->hasCustomProperty('priority'))
                            <span
                                class="mle-pseudo-button mle-pseudo-button-icon"
                                title="{{ __('media-library-extensions::messages.set-as-main') }}"
                            >
                                {{ $medium->getCustomProperty('priority') + 1 }}
                            </span>
                        @endif
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
                    @if(isMediaType($medium, 'image') && !isMediaType($medium, 'youtube-video'))
                        <button
                            type="button"
                            class="mle-button mle-button-icon btn btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#{{$id}}-iem-{{$medium->id}}"
                            title="{{ __('media-library-extensions::messages.edit') }}"
                            @disabled($disabled)
                        >
                            <x-mle-shared-icon
                                name="{{ config('media-library-extensions.icons.edit') }}"
                                title="{{ __('media-library-extensions::messages.edit') }}"
                            />
                        </button>
                    @endif
                    @if($getConfig('showSetAsFirstButton'))
                        @if($medium->getCustomProperty('priority') === 0)
                            <button
                                type="button"
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
                                :collections="$collections"
                                :options="$options"
                                :disabled="$disabled"
                                :show-set-as-first-button="$getConfig('showSetAsFirstButton')"
                                :show-media-edit-button="$getConfig('showMediaEditButton')"
                                :frontend-theme="$getConfig('frontendTheme')"
                                :use-xhr="$getConfig('useXhr')"
                            />
                        @endif
                    @endif
                    @if($getConfig('showDestroyButton'))
                        <x-mle-partial-destroy-form
                            :id="$id"
                            :medium="$medium"
                            :collections="$collections"
                            :options="$options"
                            :disabled="$disabled"
                            :frontend-theme="$getConfig('frontendTheme')"
                            :use-xhr="$getConfig('useXhr')"
                        />
                    @endif
                </div>
            </div>
        @endif
    </div>
@empty
    <div class="mlbrgn-mle-component media-manager-preview-media-container media-manager-no-media">
        <span class="mle-no-media">{{ __('media-library-extensions::messages.no_media') }}</span>
    </div>
@endforelse
<x-mle-media-modal
    :id="$id"
    :model-or-class-name="$modelOrClassName"
{{--    :media-collection="$imageCollection"--}}
    :media-collections="$collections"
    :video-auto-play="true"
    :frontend-theme="$getConfig('frontendTheme')"
    title="Media carousel"
/>
