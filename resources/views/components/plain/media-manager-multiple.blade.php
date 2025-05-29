<div
    id="{{ $id }}"
    {{ $attributes->class([
        'mlbrgn-mle-component media-manager media-manager-multiple',
    ]) }}>

    <x-mle_internal-debug/>

    @if(!empty($title))
        <h2 class="media-manager-heading">{{ $title }}</h2>
    @endif
    <div class="media-manager-row media-manager-multiple-row">
        @if($uploadEnabled && !is_null($uploadRoute))
            <form
                class="media-manager-form media-manager-multiple-form"
                action="{{ $uploadRoute }}"
                enctype="multipart/form-data"
                method="post">
                @csrf
                <input
                    accept="{{ $allowedMimeTypes }}"
                    name="{{ $uploadFieldName }}[]"
                    type="file"
                    class="media-manager-input-file form-control"
                    multiple/>
                <input
                    type="hidden"
                    name="collection_name"
                    value="{{ $mediaCollection }}"/>
                <input
                    type="hidden"
                    name="model_type"
                    value="{{ get_class($model) }}"/>
                <input
                    type="hidden"
                    name="model_id"
                    value="{{ $model->id }}"/>
                <input
                    type="hidden"
                    name="target_id"
                    value="{{ $id }}"/>
                <button
                    type="submit"
                    class="">
                    {{ __('media-library-extensions::messages.upload-media') }}
                </button>
                <x-mle_internal-flash :target-id="$id"/>
            </form>
            @if($media->count() === 0)
                <p class="media-manager-no-media">
                    {{ __('media-library-extensions::messages.no-media') }}
                </p>
            @endif
        @endif

        @if($media->count() > 0)
            {{-- Preview of all images in grid --}}
            <div class="media-manager-preview-wrapper media-manager-multiple-preview-wrapper">
                <div class="media-manager-preview-images">
                    @foreach($media as $medium)
                        <div class="media-manager-preview-image-container">
                            <div class="mm-something">
                                <a
                                    class="previewed-image cursor-zoom-in">
                                    <x-mle-image-responsive :medium="$medium" />
                                </a>
                            </div>
                            @if($setAsFirstEnabled && $showOrder)
                                <span>{{ $medium->order_column }}</span>
                            @endif
                            <div class="media-manager-preview-menu media-manager-multiple-preview-menu">
                                <div>
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
                                            <form
                                                class="media-manager-menu-form"
                                                action="{{ route(mle_prefix_route('set-as-first')) }}"
                                                method="post">
                                                @csrf
                                                <input type="hidden" name="medium_id" value="{{ $medium->id }}">
                                                <input type="hidden" name="collection_name" value="{{ $mediaCollection }}">
                                                <input type="hidden" name="model_type" value="{{ get_class($model) }}">
                                                <input type="hidden" name="model_id" value="{{ $model->id }}">
                                                <input type="hidden" name="target_id" value="{{ $id }}"/>
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
                                <div class="media-manager-preview-image-menu-end">
                                    @if($destroyEnabled)
                                        <form
                                            class="media-manager-preview-form media-manager-multiple-preview-form"
                                            action="{{ route(mle_prefix_route('medium-destroy'), $medium->id) }}"
                                            method="post">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="target_id" value="{{ $id }}"/>
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
                <x-mle-media-previewer-modal
                    :id="$modalId"
                    :model="$model"
                    :media-collection="$mediaCollection"
                    title="Media carousel"/>
            </div>
        @endif
    </div>
    @if(!$uploadEnabled && $media->count() === 0)
        <span>{{ __('media-library-extensions::messages.no-media') }}</span>
    @endif
</div>

@once
    <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app.css') }}">
@endonce
