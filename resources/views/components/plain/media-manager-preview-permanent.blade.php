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

        @if($medium->hasCustomProperty('youtube-id'))
            <div
                class="media-manager-preview-item-container"
                data-modal-trigger="#{{$id}}-modal"
                data-slide-to="{{ $loop->index }}"
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
            @if(isMediaType($medium, 'document'))
                <div
                    data-modal-trigger="#{{$id}}-modal"
                    data-slide-to="{{ $loop->index }}"
                    class="media-manager-preview-item-container"
                >
                    <x-mle-document :medium="$medium"
                                    class="previewed-document mle-cursor-zoom-in"
                                    data-bs-target="#{{ $id }}-modal-carousel"
                                    data-bs-slide-to="{{ $loop->index }}"
                    />
                </div>
            @elseif(isMediaType($medium, 'video'))
                <div
                    data-bs-toggle="modal"
                    data-bs-target="#{{$id}}-modal"
                    class="media-manager-preview-item-container"
                >
                    <video controls class="media-manager-video-preview" preload="metadata">
                        <source src="{{ $medium->getUrl() }}" type="{{ $medium->mime_type }}">
                        Your browser does not support the video tag.
                    </video>
                </div>
            @elseif(isMediaType($medium, 'audio'))
                <div
                    data-bs-toggle="modal"
                    data-bs-target="#{{$id}}-modal"
                    class="media-manager-preview-item-container"
                >
                    <div class="media-manager-audio-preview">
                        <audio controls>
                            <source src="{{ $medium->getUrl() }}" type="{{ $medium->mime_type }}">
                            {{ __('media-library-extensions::messages.your_browser_does_not_support_the_audio_element') }}
                        </audio>
                    </div>
{{--                    <x-mle-audio />--}}
                </div>
            @elseif(isMediaType($medium, 'image'))
                <div
                    data-modal-trigger="#{{$id}}-modal"
                    data-slide-to="{{ $loop->index }}"
                    class="media-manager-preview-item-container"
                >
                    <x-mle-image-responsive
                        :medium="$medium"
                        class="media-manager-image-preview mle-cursor-zoom-in"
                        data-bs-target="#{{$id}}-modal-carousel"
                        data-bs-slide-to="{{ $loop->index }}"
                        draggable="false"
                    />
                </div>
                {{-- TODO title --}}
                <x-mle-image-editor-modal
                    title=""
                    :initiator-id="$id"
                    id="{{ $id }}" 
                    :medium="$medium" 
                    :model-or-class-name="$modelOrClassName"
                    :image-collection="$imageCollection"
                    :document-collection="$documentCollection"
                    :youtube-collection="$youtubeCollection"
                    :frontend-theme="$frontendTheme"
                />
            @else
                {{ __('media-library-extensions::messages.non_supported_file_format') }}
            @endif
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
                    @if(isMediaType($medium, 'image') && !$medium->hasCustomProperty('youtube-id'))
                        <button
                            type="button"
                            data-modal-trigger="#{{$id}}-image-editor-modal-{{$medium->id}}"
                            class="mle-button mle-button-icon btn btn-primary"
                            title="{{ __('media-library-extensions::messages.edit') }}"
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
                            <x-mle-partial-set-as-first-form
                                :medium="$medium"
                                :id="$id"
                                :model="$model"
                                :target-media-collection="$imageCollection"
                                :image-collection="$imageCollection"
                                :document-collection="$documentCollection"
                                :youtube-collection="$youtubeCollection"
                                :set-as-first-enabled="$setAsFirstEnabled"
                            />
                        @endif
                    @endif
                    @if($destroyEnabled)
                        <x-mle-partial-destroy-form
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
<x-mle-media-modal
    :id="$id"
    :model-or-class-name="$modelOrClassName"
    :media-collection="$imageCollection"
    :media-collections="[$imageCollection, $youtubeCollection, $documentCollection]"
    :video-auto-play="true"
    :frontend-theme="$frontendTheme"
    title="Media carousel"/>
