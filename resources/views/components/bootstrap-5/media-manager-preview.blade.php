@foreach($media as $medium)
    <div 
        class="mlbrgn-mle-component media-manager-preview-media-container"
    {{--    data-set-as-first-route="{{ $mediumSetAsFirstRoute }}"--}}
    {{--    data-destroy-route="{{ $mediumDestroyRoute }}"--}}
    >
        
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
                    data-bs-slide-to="{{ $loop->index }}"
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
                                    data-bs-slide-to="{{ $loop->index }}"
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
{{--                    <x-mle-partial-set-as-first-form--}}
{{--                        :medium="$medium"--}}
{{--                        :id="$id"--}}
{{--                        :set-as-first-enabled="$setAsFirstEnabled"--}}
{{--                        :media-collection="$mediaCollection"--}}
{{--                        :model="$model"--}}
{{--    --}}{{--                        :youtube-collection="$youtubeCollection"--}}
{{--    --}}{{--                        :document-collection="$documentCollection"--}}
{{--                    />--}}
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