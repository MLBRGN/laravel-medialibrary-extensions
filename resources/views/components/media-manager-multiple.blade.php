<div {{ $attributes->class([
        mle_media_class('media-manager-multiple-wrapper'),
        'mlbrgn-mle'
    ]) }}>
    <x-mle_internal-debug/>
    @if(!empty($title))
        <h2 class="@mediaClass('media-manager-headings')">{{ $title }}</h2>
    @endif

    <div class="@mediaClass('media-manager-multiple-row')">
        @if($uploadEnabled && !is_null($uploadRoute))
            <form
                class="@mediaClass('media-manager-multiple-form')"
                action="{{ $uploadRoute }}"
                enctype="multipart/form-data"
                method="post">
                @csrf
                <input
                    accept="{{ $allowedMimeTypes }}"
                    name="{{ $uploadFieldName }}[]"
                    type="file"
                    class="@mediaClass('media-manager-input-file')"
                    multiple/>
                <input
                    type="hidden"
                    name="collection_name"
                    value="{{ $mediaCollectionName }}"/>
                <input
                    type="hidden"
                    name="model_type"
                    value="{{ get_class($model) }}"/>
                <input
                    type="hidden"
                    name="model_id"
                    value="{{ $model->id }}"/>
                <button
                    type="submit"
                    class="@mediaClass('media-manager-button-upload')">
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
                <div class="@mediaClass('media-manager-preview-images')">
                    @foreach($media as $medium)
                        <div class="@mediaClass('media-manager-preview-image-container')">
                            <div
                                data-bs-toggle="modal"
                                data-bs-target="#{{$modalId}}">
                                <a
                                    class="previewed-image cursor-zoom-in"
                                    data-bs-target="#{{$modalId}}-carousel"
                                    data-bs-slide-to="{{ $loop->index }}">
                                    <x-mle-image-responsive :medium="$medium" />
                                </a>
                            </div>
                            @if($setAsFirstEnabled && $showOrder)
                                <span class="">{{ $medium->order_column }}</span>
                            @endif
                            <div class="@mediaClass('media-manager-multiple-preview-menu')">
                                <div class="">
                                        @if($setAsFirstEnabled)
                                            @if($medium->order_column === $media->min('order_column'))
                                                <button
                                                    class=""
                                                    title="{{ __('media-library-extensions::messages.set-as-main') }}"
                                                    disabled>
                                                    <x-mle_internal-icon
                                                        name="{{ config('media-library-extensions.icons.set-as-main') }}"
                                                        title="{{ __('media-library-extensions::messages.set-as-main') }}"
                                                    />
                                                </button>
                                            @else
                                            <form class="@mediaClass('media-manager-menu-form')" action="{{ route(mle_prefix_route('set-as-first')) }}" method="post">
                                                @csrf
                                                <input
                                                    type="hidden"
                                                    name="medium_id"
                                                    value="{{ $medium->id }}">
                                                <input
                                                    type="hidden"
                                                    name="collection_name"
                                                    value="{{ $mediaCollectionName }}">
                                                <input
                                                    type="hidden"
                                                    name="model_type"
                                                    value="{{ get_class($model) }}">
                                                <input
                                                    type="hidden"
                                                    name="model_id"
                                                    value="{{ $model->id }}">
                                                <button
                                                    type="submit"
                                                    class=""
                                                    title="{{ __('media-library-extensions::messages.setup-as-main') }}">
                                                    <x-mle_internal-icon
                                                        name="{{ config('media-library-extensions.icons.setup-as-main') }}"
                                                        title="{{ __('media-library-extensions::messages.setup-as-main') }}"
                                                    />
                                                </button>
                                            </form>
                                            @endif
                                        @endif
                                </div>
                                <div class="media-manager-preview-image-menu-end d-flex align-items-center gap-1">
                                    @if($destroyEnabled)
                                        <form
                                            class="@mediaClass('media-manager-multiple-preview-medium-form')"
                                            action="{{ route(mle_prefix_route('medium-destroy'), $medium->id) }}"
                                            method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class=""
                                                title="{{ __('media-library-extensions::messages.delete_medium') }}">
                                                <x-mle_internal-icon
                                                    name="{{ config('media-library-extensions.icons.delete') }}"
                                                    :title="__('media-library-extensions::messages.delete_medium')"
                                                 />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <x-mle-media-manager-preview-modal
                    :modal-id="$modalId"
                    :model="$model"
                    :media-collection-name="$mediaCollectionName"
                    title="Media carousel"/>
            </div>
        @endif
    </div>
    @if(!$uploadEnabled && $media->count() === 0)
        <span>{{ __('media-library-extensions::messages.no-media') }}</span>
    @endif
</div>
