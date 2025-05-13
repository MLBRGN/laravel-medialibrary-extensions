<div {{ $attributes->class([mle_media_class('media-manager-multiple-wrapper') ]) }}>
    @if(!empty($title))
        <h2 class="mb-5">{{ $title }}</h2>
    @endif
    <div class="@mediaClass('media-manager-multiple-row')">
        @if($uploadEnabled && !is_null($uploadRoute))
            <form class="@mediaClass('media-manager-multiple-form')" action="{{ $uploadRoute }}" enctype="multipart/form-data" method="post">
                @csrf
                <input accept="{{ $allowedMimeTypes }}" name="{{ $uploadFieldName }}[]" type="file"  class="@mediaClass('media-manager-input-file')" multiple/>
                <input type="hidden" name="collection_name" value="{{ $mediaCollectionName }}"/>
                <input type="hidden" name="model_type" value="{{ get_class($model) }}"/>
                <input type="hidden" name="model_id" value="{{ $model->id }}"/>
                <button type="submit" class="@mediaClass('media-manager-button-upload')">
                    {{  __('media-library-extensions::messages.upload-media') }}
                </button>
            </form>
            @if($media->count() === 0)
                <p class="@mediaClass('media-manager-no-media')">
                    {{ __('media-library-extensions::messages.no-media') }}
                </p>
            @endif
        @endif

        @if($media->count() > 0)
            {{-- Preview of all images in grid --}}
            <div class="@mediaClass('media-manager-multiple-preview-wrapper')">
                <div class="media-manager-preview-images">
                    @foreach($media as $medium)
                        <div class="media-manager-preview-image-container">
                            <div data-bs-toggle="modal" data-bs-target="#{{$modalId}}">
                                <a class="previewed-image cursor-zoom-in" data-bs-target="#{{$modalId}}-carousel" data-bs-slide-to="{{ $loop->index }}">
                                    {{ $medium->img()->lazy()->attributes(['class' => 'w-100 h-100 object-cover']) }}
                                </a>
                            </div>
                            @if($setAsFirstEnabled && $showOrder)
{{--                                    <span class="media-manager-order">{{ $medium->order_column }}</span>--}}
                                <span class="">{{ $medium->order_column }}</span>
                            @endif
{{--                                <div class="media-manager-preview-image-menu d-flex justify-content-between px-2 align-items-center">--}}
                            <div class="@mediaClass('media-manager-multiple-preview-menu')">
{{--                                    <div class="media-manager-preview-image-menu-start">--}}
                                <div class="">
                                        @if($setAsFirstEnabled)
                                            @if($medium->order_column === $media->min('order_column'))
{{--                                                    <button class="btn btn-icon btn-icon-dummy btn-sm" title="Ingesteld als hoofdafbeelding" disabled>--}}
                                                <button class="" title="Ingesteld als hoofdafbeelding" disabled>
                                                    Hoofdafbeelding
{{--                                                        <svg class="fill-update" width="16" height="16">--}}
{{--                                                            <use href="{{ url('/images/svg-sprites/bootstrap-icons-backend-sprite.svg?1#star-fill') }}"></use>--}}
{{--                                                        </svg>--}}
                                                </button>
                                            @else
                                            <form class="media-manager-menu-form" action="{{ route(mle_prefix_route('set-as-first')) }}" method="post">
                                                @csrf
                                                <input type="hidden" name="medium_id" value="{{ $medium->id }}">
                                                <input type="hidden" name="collection_name" value="{{ $mediaCollectionName }}">
                                                <input type="hidden" name="model_type" value="{{ get_class($model) }}">
                                                <input type="hidden" name="model_id" value="{{ $model->id }}">
                                                <button type="submit" class="">
                                                    Stel in als hoofdafbeelding
{{--                                                        <svg width="16" height="16">--}}
{{--                                                            <use href="{{ url('/images/svg-sprites/bootstrap-icons-backend-sprite.svg?1#star') }}"></use>--}}
{{--                                                        </svg>--}}
                                                </button>
                                            </form>
                                            @endif
                                        @endif
                                </div>
                                <div class="media-manager-preview-image-menu-end d-flex align-items-center gap-1">
                                    @if($destroyEnabled)
                                        <form class="@mediaClass('media-manager-multiple-preview-medium-form')" action="{{ route(mle_prefix_route('medium-destroy'), $medium->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="@mediaClass('media-manager-button-icon-delete')">
                                                {{ __('media-library-extensions::messages.delete_medium') }}
{{--                                                    <svg width="16" height="16">--}}
{{--                                                        <use href="{{ url('/images/svg-sprites/bootstrap-icons-backend-sprite.svg#trash') }}"></use>--}}
{{--                                                    </svg>--}}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <x-media-library-extensions::preview-modal :modal-id="$modalId" :model="$model" :media-collection-name="$mediaCollectionName" title="Media carousel"/>
            </div>
        @endif
    </div>
    @if(!$uploadEnabled && $media->count() === 0)
        <span>{{ __('media-library-extensions::messages.no-media') }}</span>
    @endif
</div>
