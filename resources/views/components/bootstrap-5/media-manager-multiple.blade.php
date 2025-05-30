{{--<pre>--}}
{{--        Media manager multiple--}}
{{--        id - {{ $id }}--}}
{{--    </pre>--}}
<div
    id="{{ $id }}"
    {{ $attributes->class([
        'media-manager media-manager-multiple container-fluid px-0',
        'mlbrgn-mle-component'
    ]) }}>

    <x-mle_internal-debug/>

    @if(!empty($title))
        <h2 class="media-manager-heading">{{ $title }}</h2>
    @endif
    <div class="media-manager-row media-manager-multiple-row row">
        @if($uploadEnabled)
            <x-mle_internal-media-manager-upload-form
                :allowedMimeTypes="$allowedMimeTypes" 
                :mediaCollection="$mediaCollection" 
                :model="$model" 
                :id="$id"
                :multiple="true"/>
            @if($media->count() === 0)
                <p class="media-manager-no-media">
                    {{ __('media-library-extensions::messages.no-media') }}
                </p>
            @endif
        @endif

        @if($media->count() > 0)
            {{-- Preview of all images in grid --}}
            <div class="media-manager-preview-wrapper media-manager-multiple-preview-wrapper col-12 col-sm-8">
                <div class="media-manager-preview-images">
                    @foreach($media as $medium)
                        <div class="media-manager-preview-image-container">
                            <div
                                data-bs-toggle="modal"
                                data-bs-target="#{{$id}}-modal"
                                class="mm-something">
                                <a
                                    class="previewed-image cursor-zoom-in"
                                    data-bs-target="#{{$id}}-modal-carousel"
                                    data-bs-slide-to="{{ $loop->index }}">
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
                                                       value="{{ $id }}"/>
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
                                       <x-mle_internal-media-manager-destroy-form :medium="$medium" :id="$id"/>
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
