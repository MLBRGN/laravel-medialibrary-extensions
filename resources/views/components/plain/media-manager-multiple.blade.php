<div class="mlbrgn-mle-component">
    <div
        id="{{ $id }}"
        {{ $attributes->class([
            'media-manager media-manager-multiple',
            'container-fluid px-0',
        ]) }}>

        <x-mle-partial-debug/>

        <div class="media-manager-row media-manager-multiple-row row">

            <div class="media-manager-form col-12 col-md-4">
                @if($uploadEnabled)
                    <x-mle-partial-upload-form
                        :allowedMimeTypes="$allowedMimeTypes"
                        :mediaCollection="$mediaCollection"
                        :model="$model"
                        :id="$id"
                        :multiple="true"/>
                @endif
                @if($youTubeSupport)
                    <x-mle-partial-youtube-upload-form
                        class="mt-3"
                        mediaCollection="workplace-youtube-videos"
                        :model="$model"
                        :id="$id"
                    />
                @endif
            </div>

            <div class="media-manager-preview-wrapper media-manager-multiple-preview-wrapper col-12 col-sm-8">
                @if($media->count() > 0)
                    <div class="media-manager-preview-images">
                        @foreach($media as $index => $medium)
                            <div class="media-manager-preview-image-container">
                                <div
                                    class="previewed-image mle-cursor-zoom-in"
                                    data-modal-id="{{ $id }}-modal"
                                    data-slide-to="{{ $loop->index }}"
                                    data-modal-trigger="{{ $id }}-modal">
                                    <x-mle-image-responsive :medium="$medium" />
                                </div>

                                <div class="media-manager-preview-menu media-manager-multiple-preview-menu">
                                    <div>
                                        @if($setAsFirstEnabled && $showOrder)
                                            <div class="media-manager-order">{{ $medium->order_column }}</div>
                                        @endif
                                        @if($setAsFirstEnabled)
                                            @if($medium->order_column === $media->min('order_column'))
                                                <button class="mle-btn-icon" title="{{ __('media-library-extensions::messages.set-as-main') }}" disabled>
                                                    <x-mle-partial-icon name="{{ config('media-library-extensions.icons.set-as-main') }}"/>
                                                </button>
                                            @else
                                                <form class="media-manager-menu-form" action="{{ route(mle_prefix_route('set-as-first')) }}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="medium_id" value="{{ $medium->id }}">
                                                    <input type="hidden" name="collection_name" value="{{ $mediaCollection }}">
                                                    <input type="hidden" name="model_type" value="{{ get_class($model) }}">
                                                    <input type="hidden" name="model_id" value="{{ $model->id }}">
                                                    <input type="hidden" name="target_id" value="{{ $id }}">
                                                    <button type="submit" class="mle-btn-icon" title="{{ __('media-library-extensions::messages.medium_set_as_main') }}">
                                                        <x-mle-partial-icon name="{{ config('media-library-extensions.icons.setup_as_main') }}"/>
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
                            </div>
                        @endforeach
                    </div>

                    <x-mle-media-modal
                        :id="$id"
                        :model="$model"
                        :media-collection="$mediaCollection"
                        title="Media carousel"
                        :media="$media"
                        :inModal="true"
                        :plainJs="true" />
                @else
                    <span>{{ __('media-library-extensions::messages.no_media') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>
<x-mle-partial-assets include-css="true" include-js="true"/>

