{{--This file will handle shared logic and delegate some UI to a partial.--}}
<div id="{{ $id }}" {{ $attributes->class([
        'media-manager-single-wrapper',
        'mlbrgn-mle-component'
    ]) }}>
    {{--    <pre>{{ var_dump(session()->all()) }}</pre>--}}
    <x-mle_internal-debug/>
    @if(!empty($title))
        <h2 class="media-manager-heading">{{ $title }}</h2>
    @endif

    <div class="media-manager-row media-manager-single-row row">
        @if($uploadEnabled && !is_null($uploadRoute))
            <form
                method="POST"
                action="{{ $uploadRoute }}"
                enctype="multipart/form-data"
                class="media-manager-form media-manager-single-form col-12 col-md-4">
                @csrf
                <input
                    type="file"
                    accept="{{ $allowedMimeTypes }}"
                    name="{{ $uploadFieldName }}"
                    class="media-manager-input-file form-control">
                <input
                    type="hidden"
                    name="collection_name"
                    value="{{ $mediaCollection }}">
                <input
                    type="hidden"
                    name="model_type"
                    value="{{ get_class($model) }}">
                <input
                    type="hidden"
                    name="model_id"
                    value="{{ $model->id }}">
                <input
                    type="hidden"
                    name="target_id"
                    value="{{ $id }}"/>
                <button
                    type="submit"
                    class="btn btn-success">
                    {{ trans_choice('media-library-extensions::messages.upload-or-replace', is_null($medium) ? 0 : 1) }}
                </button>
                <x-mle_internal-flash :target-id="$id"/>
            </form>
            @if(!$medium)
                <p class="media-manager-no-media">
                    {{ __('media-library-extensions::messages.no-medium') }}
                </p>
            @endif
        @endif

        @if($medium)
            <div class="media-manager-preview-wrapper media-manager-single-preview-wrapper col-12 col-md-8 text-center">
                <a
                    class="media-manager-preview-medium-link media-manager-single-preview-medium-link cursor-zoom-in"
                    data-bs-toggle="modal"
                    data-bs-target="#{{$modalId}}">
                    <img
                        src="{{ $medium->getUrl() }}"
                        class="media-manager-preview-medium media-manager-single-preview-medium image-fluid"
                        alt=" {{ __('media-library-extensions::messages.no-medium') }}">
                </a>
                <div class="media-manager-preview-menu media-manager-single-preview-menu">
                    @if($destroyEnabled && !is_null($destroyRoute))
                        <form class="media-manager-preview-form media-manager-single-preview-form"
                              action="{{ $destroyRoute }}"
                              method="post">
                            @csrf
                            @method('DELETE')
                            <input
                                type="hidden"
                                name="target_id"
                                value="{{ $id }}"/>
                            <button
                                type="submit"
                                class="button-icon-delete btn btn-delete btn-icon btn-icon-delete btn-sm">
                                {{ __('media-library-extensions::messages.delete_medium') }}
                            </button>
                        </form>
                    @endif
                </div>
                <x-mle-media-manager-preview-modal
                    :id="$modalId"
                    :model="$model"
                    :media-collection="$mediaCollection"
                    title="Media carousel"/>
            </div>
        @endif
    </div>

    @if(!$uploadEnabled && !$medium)
        <span>{{ __('media-library-extensions::messages.no-medium') }}</span>
    @endif
</div>

@once
    <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app.css') }}">
@endonce
