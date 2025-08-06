@forelse ($media as $medium)
    <div
        {{ $attributes->class([
            'mlbrgn-mle-component',
             'theme-'.$theme,
             'media-manager-preview-media-container'
        ]) }}
        data-temporary-upload-set-as-first-route="{{ route(mle_prefix_route('temporary-upload-set-as-first'), $medium) }}"
        data-temporary-upload-destroy-route="{{ route(mle_prefix_route('temporary-upload-destroy'), $medium) }}"
    >
        @if($medium->isYouTubeVideo())
            <div
                class="media-manager-preview-item-container"
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-modal"
            >
                <x-mle-video-youtube
                    class="mle-video-responsive mle-cursor-zoom-in"
                    :medium="$medium"
                    :preview="true"
                    :youtube-id="$medium->getCustomProperty('youtube-id')"
                    :youtube-params="[]"
                    data-bs-target="#{{$id}}-modal-carousel"
                    data-bs-slide-to="{{ $loop->index }}"
                />
            </div>
        @else
            @if($medium->isDocument())
                <div
                    data-bs-toggle="modal"
                    data-bs-target="#{{$id}}-modal"
                    class="media-manager-preview-item-container"
                >
                    <x-mle-document :medium="$medium"
                                    class="previewed-document mle-cursor-zoom-in"
                                    data-bs-target="#{{ $id }}-modal-carousel"
                                    data-bs-slide-to="{{ $loop->index }}"
                    />
                </div>
            @elseif($medium->isImage())
                <div
                    data-bs-toggle="modal"
{{--                    data-bs-target="#{{$id}}-modal"--}}
                    class="media-manager-preview-item-container"
                >
                    <img 
                        src="{{ $medium->getFullUrl() }}" 
                        class="media-manager-image-preview mle-cursor-zoom-in" 
                        alt="{{ $medium->name }}"
                        data-bs-target="#{{$id}}-modal-carousel"
                        data-bs-slide-to="{{ $loop->index }}"
                    />
                  
                </div>
                <x-mle-image-editor-modal
                    title="TODO"
                    :initiator-id="$id"
                    id="{{ $id }}" 
                    :medium="$medium" 
                    :model-or-class-name="$modelType"
                    :image-collection="$imageCollection"
                    :document-collection="$documentCollection"
                    :youtube-collection="$youtubeCollection"
                />
            @else
                no suitable type
            @endif
            @if($showMenu)
                <div class="media-manager-preview-menu">
                    <div class="media-manager-preview-image-menu-start">
                        @if($showOrder)
                            <span
                                class="mle-pseudo-button mle-pseudo-button-icon"
                                title="{{ __('media-library-extensions::messages.set-as-main') }}"
                            >
                            {{ $medium->order_column }}
                            </span>
                        @endif
                    </div>
                    <div class="media-manager-preview-image-menu-end">
                        @if($medium->isImage() && !$medium->isYouTubeVideo())
                            <button
                                type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#{{$id}}-image-editor-modal-{{$medium->id}}"
                                class="mle-button mle-button-icon btn btn-primary"
                                title="{{ __('media-library-extensions::messages.edit') }}"
                                data-action="edit-image"
                            >
                                <x-mle-partial-icon
                                    name="{{ config('media-library-extensions.icons.edit') }}"
                                    title="{{ __('media-library-extensions::messages.edit') }}"
                                />
                            </button>
                        @endif
                        @if($setAsFirstEnabled)
                            @if($medium->order_column === $media->min('order_column'))
                                <button
                                    class="mle-button mle-button-icon btn btn-primary"
                                    title="{{ __('media-library-extensions::messages.set-as-main') }}"
                                    disabled>
                                    <x-mle-partial-icon
                                        name="{{ config('media-library-extensions.icons.set-as-main') }}"
                                        title="{{ __('media-library-extensions::messages.medium_set_as_main') }}"
                                    />
                                </button>
                            @else
                                <x-mle-partial-temporary-upload-set-as-first-form
                                    :medium="$medium"
                                    :id="$id"
{{--                                    :model="$model"--}}
                                    :target-media-collection="$imageCollection"
                                    :image-collection="$imageCollection"
                                    :document-collection="$documentCollection"
                                    :youtube-collection="$youtubeCollection"
                                    :set-as-first-enabled="$setAsFirstEnabled"
                                />
                                @endif
                            @endif
                        @endif
                        @if($destroyEnabled)
                            <x-mle-partial-temporary-upload-destroy-form
                                :medium="$medium"
                                :id="$id"
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
