<div class="mlbrgn-mle-component media-manager-preview-media-container">
    @if($medium->hasCustomProperty('youtube-id'))
        <div
            data-bs-toggle="modal"
            data-bs-target="#{{$id}}-modal"
            class="media-manager-preview-item-container">
            <x-mle-video-youtube
                class="mle-video-responsive mle-cursor-zoom-in"
                :medium="$medium"
                :preview="true"
                :youtube-id="$medium->getCustomProperty('youtube-id')"
                :youtube-params="[]"
                data-bs-target="#{{$id}}-modal-carousel"
                data-bs-slide-to="{{ $loopIndex }}"
            />
        </div>
    @else
        @if(isMediaType($medium, 'document'))
            <div
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-modal"
                class="media-manager-preview-item-container">
                <x-mle-document :medium="$medium"
                                class="previewed-document mle-cursor-zoom-in"
                                data-bs-target="#{{ $id }}-modal-carousel"
                                data-bs-slide-to="{{ $loopIndex }}"
                />
            </div>
        @elseif(isMediaType($medium, 'image'))
            <div
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-modal"
                class="media-manager-preview-item-container">
                <x-mle-image-responsive 
                    :medium="$medium"
                    class="media-manager-image-preview mle-cursor-zoom-in"
                    data-bs-target="#{{$id}}-modal-carousel"
                    data-bs-slide-to="{{ $loopIndex }}"
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
                    @if($isFirstInCollection)
                        <button
                            class="mle-button-icon"
                            title="{{ __('media-library-extensions::messages.set-as-main') }}"
                            disabled>
                            <x-mle-partial-icon
                                name="{{ config('media-library-extensions.icons.set-as-main') }}"
                                title="{{ __('media-library-extensions::messages.medium_set_as_main') }}"
                            />
                        </button>
                    @else
                        <form
                            class="media-manager-menu-form"
                            action="{{ route(mle_prefix_route('set-as-first')) }}"
                            method="post">
                            @csrf
                            <input type="hidden"
                                   name="medium_id"
                                   value="{{ $medium->id }}">
                            <input type="hidden"
                                   name="collection_name"
                                   value="{{ $mediaCollection }}">
                            <input type="hidden"
                                   name="model_type"
                                   value="{{ get_class($model) }}">
                            <input type="hidden"
                                   name="model_id"
                                   value="{{ $model->id }}">
                            <input type="hidden"
                                   name="target_id"
                                   value="{{ $id }}">
                            <button
                                type="submit"
                                class="mle-button-icon"
                                title="{{ __('media-library-extensions::messages.medium_set_as_main') }}">
                                <x-mle-partial-icon
                                    name="{{ config('media-library-extensions.icons.setup_as_main') }}"
                                    title="{{ __('media-library-extensions::messages.setup_as_main') }}"
                                />
                            </button>
                        </form>
                    @endif
                @endif
            </div>
            <div class="media-manager-preview-image-menu-end d-flex align-items-center gap-1">
                @if($destroyEnabled)
                    <x-mle-partial-destroy-form :medium="$medium" :id="$id"/>
                @endif
            </div>
        </div>
    @endif
</div>