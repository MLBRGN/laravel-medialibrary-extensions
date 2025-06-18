@foreach($media as $medium)
    <div 
        class="mlbrgn-mle-component media-manager-preview-media-container"
        data-set-as-first-route="{{ route(mle_prefix_route('set-as-first'), $medium) }}"
        data-destroy-route="{{ route(mle_prefix_route('medium-destroy'), $medium) }}"
    >
        
        @if($medium->hasCustomProperty('youtube-id'))
            
            <div
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-modal"
                class="media-manager-preview-item-container"
{{--                data-set-as-first-route="{{ route(mle_prefix_route('set-as-first'), $medium) }}"--}}
{{--                data-destroy-route="{{ route(mle_prefix_route('medium-destroy'), $medium) }}"--}}
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
                    data-bs-toggle="modal"
                    data-bs-target="#{{$id}}-modal"
                    class="media-manager-preview-item-container"
{{--                    data-set-as-first-route="{{ route(mle_prefix_route('set-as-first'), $medium) }}"--}}
{{--                    data-destroy-route="{{ route(mle_prefix_route('medium-destroy'), $medium) }}"--}}
                >
                    <x-mle-document :medium="$medium"
                                    class="previewed-document mle-cursor-zoom-in"
                                    data-bs-target="#{{ $id }}-modal-carousel"
                                    data-bs-slide-to="{{ $loop->index }}"
                    />
                </div>
            @elseif(isMediaType($medium, 'image'))
                <div
                    data-bs-toggle="modal"
                    data-bs-target="#{{$id}}-modal"
                    class="media-manager-preview-item-container"
{{--                    data-set-as-first-route="{{ route(mle_prefix_route('set-as-first'), $medium) }}"--}}
{{--                    data-destroy-route="{{ route(mle_prefix_route('medium-destroy'), $medium) }}"--}}
                >
                    <x-mle-image-responsive
                        :medium="$medium"
                        class="media-manager-image-preview mle-cursor-zoom-in"
                        data-bs-target="#{{$id}}-modal-carousel"
                        data-bs-slide-to="{{ $loop->index }}"
                        draggable="false"
                    />
                </div>
            @else
                no suitable type
            @endif
        @endif
        @if($showMenu)
            <div class="media-manager-preview-menu">
                <div>
                    @if($setAsFirstEnabled && $showOrder)
                        <div class="media-manager-order">{{ $medium->order_column }}</div>
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
                                :set-as-first-enabled="$setAsFirstEnabled"
                                :media-collection="$mediaCollection"
                                :model="$model"
                                :youtube-collection="$youtubeCollection"
                                :document-collection="$documentCollection"
                            />
                        @endif
                    @endif
                </div>
                <div class="media-manager-preview-image-menu-end d-flex align-items-center gap-1">
                    @if($destroyEnabled)
                        <x-mle-partial-destroy-form 
                            :medium="$medium" 
                            :id="$id"
    {{--                            data-destroy-route="{{ $mediumDestroyRoute }}"--}}
                        />
                    @endif
                </div>
            </div>
        @endif
    </div>
@endforeach
<x-mle-media-modal
    :id="$id"
    :model="$model"
    :media-collection="$mediaCollection"
    :media-collections="[$mediaCollection, $youtubeCollection, $documentCollection]"
    title="Media carousel"/>