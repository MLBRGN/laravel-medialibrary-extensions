{{--This file will handle shared logic and delegate some UI to a partial.--}}
<div id="{{ $id }}" {{ $attributes->class([
        mle_media_class('media-manager-single-wrapper'),
       'mlbrgn-mle'
    ]) }}>
{{--    <pre>{{ var_dump(session()->all()) }}</pre>--}}
    <x-mle_internal-debug/>
    @if(!empty($title))
        <h2 class="@mediaClass('media-manager-headings')">{{ $title }}</h2>
    @endif

    <div class="@mediaClass('media-manager-single-row')">
        @if($uploadEnabled && !is_null($uploadRoute))
            <form
                method="POST"
                action="{{ $uploadRoute }}"
                enctype="multipart/form-data"
                class="@mediaClass('media-manager-single-form')">
                @csrf
                <input
                    type="file"
                    accept="{{ $allowedMimeTypes }}"
                    name="{{ $uploadFieldName }}"
                    class="@mediaClass('media-manager-input-file')">
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
                    class="@mediaClass('media-manager-button-upload')">
                    {{ trans_choice('media-library-extensions::messages.upload-or-replace', is_null($medium) ? 0 : 1) }}
                </button>
                <x-mle_internal-flash :target-id="$id"/>
            </form>
            @if(!$medium)
                <p class="@mediaClass('media-manager-no-media')">
                    {{ __('media-library-extensions::messages.no-medium') }}
                </p>
            @endif
        @endif

        @if($medium)
            <div class="@mediaClass('media-manager-single-preview-wrapper')">
                <a
                    class="@mediaClass('media-manager-single-preview-medium-link')"
                    data-bs-toggle="modal"
                    data-bs-target="#{{$modalId}}">
                    <img
                        src="{{ $medium->getUrl() }}"
                        class="@mediaClass('media-manager-single-preview-medium')"
                        alt=" {{ __('media-library-extensions::messages.no-medium') }}">
                </a>
                <div class="@mediaClass('media-manager-single-preview-menu')">
                    @if($destroyEnabled && !is_null($destroyRoute))
                        <form class="@mediaClass('media-manager-single-preview-form')"
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
                                class="@mediaClass('media-manager-button-icon-delete')">
                                {{ __('media-library-extensions::messages.delete_medium') }}
                            </button>
                        </form>
                    @endif
                </div>
                <x-mle-media-manager-preview-modal
                    :modal-id="$modalId"
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
{{--    <script src="{{ asset('blogpackage/js/app.js') }}"></script>--}}
{{--        <link rel="stylesheet" href="{{ asset('vendor/medialibrary-extensions/app.css') }}">--}}
{{--    <link href="{{ mle_package_asset('css/app.css')  }}" rel="stylesheet" />--}}
   {{-- <link
        rel="stylesheet"
        href="{{ mle_package_asset('media-library-extensions.css') }}">--}}
@endonce
