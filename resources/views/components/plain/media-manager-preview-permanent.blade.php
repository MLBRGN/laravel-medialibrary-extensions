@forelse($media as $medium)
    <div 
        {{ $attributes->class([
            'mlbrgn-mle-component',
             'theme-'.$frontendTheme,
             'media-manager-preview-media-container'
        ]) }}
        data-set-as-first-route="{{ route(mle_prefix_route('set-as-first'), $medium) }}"
        data-destroy-route="{{ route(mle_prefix_route('medium-destroy'), $medium) }}"
    >
        @if(isMediaType($medium, 'youtube-video'))
            <div
                class="media-manager-preview-item-container"
                data-modal-trigger="#{{$id}}-mod"
                data-slide-to="{{ $loop->index }}"
            >
                <x-mle-video-youtube
                    class="mle-video-responsive mle-cursor-zoom-in"
                    :medium="$medium"
                    :preview="true"
                    :frontend-theme="$frontendTheme"
                />
            </div>
        @elseif(isMediaType($medium, 'document'))
            <div
                data-modal-trigger="#{{$id}}-mod"
                data-slide-to="{{ $loop->index }}"
                class="media-manager-preview-item-container"
            >
                <x-mle-document :medium="$medium"
                                class="previewed-document mle-cursor-zoom-in"
                />
            </div>
        @elseif(isMediaType($medium, 'video'))
            <div
                data-modal-trigger="#{{$id}}-mod"
                data-slide-to="{{ $loop->index }}"
                class="media-manager-preview-item-container"
            >
                <x-mle-video 
                    :medium="$medium" 
                    class="mle-cursor-zoom-in"
                />
            </div>
        @elseif(isMediaType($medium, 'audio'))
            <div
                data-modal-trigger="#{{$id}}-mod"
                data-slide-to="{{ $loop->index }}"
                class="media-manager-preview-item-container"
            >
                <x-mle-audio 
                    :medium="$medium" 
                    class="mle-cursor-zoom-in"
                />
            </div>
        @elseif(isMediaType($medium, 'image'))
            <div
                data-modal-trigger="#{{$id}}-mod"
                data-slide-to="{{ $loop->index }}"
                class="media-manager-preview-item-container"
            >
                <x-mle-image-responsive
                    :medium="$medium"
                    class="media-manager-image-preview mle-cursor-zoom-in"
                    draggable="false"
                />
            </div>
            <x-mle-image-editor-modal
                title=""
                :initiator-id="$id"
                id="{{ $id }}" 
                :medium="$medium" 
                :model-or-class-name="$modelOrClassName"
                :image-collection="$imageCollection"
                :document-collection="$documentCollection"
                :youtube-collection="$youtubeCollection"
                :audio-collection="$audioCollection"
                :video-collection="$videoCollection"
                :frontend-theme="$frontendTheme"
                :use-xhr="$useXhr"
            />
        @else
            {{ __('media-library-extensions::messages.non_supported_file_format') }}
        @endif
        @if($showMenu)
            <div class="media-manager-preview-menu">
                <div class="media-manager-preview-image-menu-start">
                    @if($showOrder)
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
                                class="mle-media-select-checkbox"
                                name="selected_media"
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
                    @if(isMediaType($medium, 'image') && !$medium->hasCustomProperty('youtube-id'))
                        <button
                            type="button"
                            data-modal-trigger="#{{$id}}-iem-{{$medium->id}}"
                            class="mle-button mle-button-icon btn btn-primary"
                            title="{{ __('media-library-extensions::messages.edit') }}"
                        >
                            <x-mle-shared-icon
                                name="{{ config('media-library-extensions.icons.edit') }}"
                                title="{{ __('media-library-extensions::messages.edit') }}"
                            />
                        </button>
                    @endif
                    @if($setAsFirstEnabled)
                        @if($medium->getCustomProperty('priority') === 0)
                            <button
                                type="button"
                                class="mle-button mle-button-icon btn btn-primary"
                                title="{{ __('media-library-extensions::messages.set-as-main') }}"
                                disabled>
                                <x-mle-shared-icon
                                    name="{{ config('media-library-extensions.icons.set-as-main') }}"
                                    title="{{ __('media-library-extensions::messages.medium_set_as_main') }}"
                                />
                            </button>
                        @else
                            <x-mle-partial-set-as-first-form
                                :medium="$medium"
                                :id="$id"
                                :model="$model"
                                :image-collection="$imageCollection"
                                :document-collection="$documentCollection"
                                :youtube-collection="$youtubeCollection"
                                :audio-collection="$audioCollection"
                                :video-collection="$videoCollection"
                                :set-as-first-enabled="$setAsFirstEnabled"
                                :frontend-theme="$frontendTheme"
                                :use-xhr="$useXhr"
                            />
                        @endif
                    @endif
                    @if($destroyEnabled)
                        <x-mle-partial-destroy-form
                            :medium="$medium"
                            :id="$id"
                            :image-collection="$imageCollection"
                            :document-collection="$documentCollection"
                            :youtube-collection="$youtubeCollection"
                            :audio-collection="$audioCollection"
                            :video-collection="$videoCollection"
                            :frontend-theme="$frontendTheme"
                            :use-xhr="$useXhr"
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
    :media-collection="$imageCollection"
    :media-collections="[$imageCollection, $youtubeCollection, $documentCollection, $videoCollection, $audioCollection]"
    :video-auto-play="true"
    :frontend-theme="$frontendTheme"
    title="Media carousel"/>
