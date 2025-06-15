<div class="mlbrgn-mle-component media-manager-preview-media-container"
   
>
    @if($medium->hasCustomProperty('youtube-id'))
        <x-mle-video-youtube
            class="mle-cursor-zoom-in"
            :medium="$medium"
            :preview="true"
            :youtube-id="$medium->getCustomProperty('youtube-id')"
            :youtube-params="[]"
            data-slide-to="{{ $loopIndex }}"
            data-modal-trigger="{{ $id }}-modal"
        />
    @else
        @if(isMediaType($medium, 'document'))
            <x-mle-document :medium="$medium"
                class="mle-cursor-zoom-in"
                data-slide-to="{{ $loopIndex }}"
                data-modal-trigger="{{ $id }}-modal"
            />
        @else
            <x-mle-image-responsive :medium="$medium"
                class="media-manager-image-preview mle-cursor-zoom-in"
                draggable="false"
                data-slide-to="{{ $loopIndex }}"
                data-modal-trigger="{{ $id }}-modal"
            />
        @endif
    @endif

    @if($showMenu)
        <div class="media-manager-preview-menu media-manager-multiple-preview-menu">
            <div>
                @if($setAsFirstEnabled && $showOrder)
                    <div class="media-manager-order">{{ $medium->order_column }}</div>
                @endif
                @if($setAsFirstEnabled)
                    @if($isFirstInCollection)
                        <button class="mle-button mle-button-icon" title="{{ __('media-library-extensions::messages.set-as-main') }}" disabled>
                            <x-mle-partial-icon 
                                name="{{ config('media-library-extensions.icons.set-as-main') }}"
                                title="{{ __('media-library-extensions::messages.medium_set_as_main') }}"
                            />
                        </button>
                    @else
                        <form class="media-manager-menu-form" action="{{ route(mle_prefix_route('set-as-first')) }}" method="post">
                            @csrf
                            <input type="hidden" name="medium_id" value="{{ $medium->id }}">
                            <input type="hidden" name="collection_name" value="{{ $mediaCollection }}">
                            <input type="hidden" name="model_type" value="{{ get_class($model) }}">
                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                            <input type="hidden" name="target_id" value="{{ $id }}">
                            <button type="submit" class="mle-button mle-button-icon" title="{{ __('media-library-extensions::messages.setup_as_main') }}">
                                <x-mle-partial-icon 
                                    name="{{ config('media-library-extensions.icons.setup_as_main') }}"
                                    title="{{ __('media-library-extensions::messages.setup_as_main') }}"
                                />
                            </button>
                        </form>
                    @endif
                @endif
            </div>
            <div class="media-manager-preview-image-menu-end">
                @if($destroyEnabled)
                    <x-mle-partial-destroy-form :medium="$medium" :id="$id"/>
                @endif
            </div>
        </div>
    @endif
</div>