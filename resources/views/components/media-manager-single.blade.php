{{--This file will handle shared logic and delegate some UI to a partial.--}}
<div {{ $attributes->class([mle_media_class('media-manager-single-wrapper') ]) }}>
    <x-mle-debug/>
    @if(!empty($title))
        <h2 class="@mediaClass('media-manager-headings')">{{ $title }}</h2>
    @endif

    <div class="@mediaClass('media-manager-single-row')">
        @if($uploadEnabled && !is_null($uploadRoute))
            <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data"
                  class="@mediaClass('media-manager-single-form')">
                @csrf
                <input type="file" accept="{{ $allowedMimeTypes }}" name="{{ $uploadFieldName }}" class="@mediaClass('media-manager-input-file')">
                <input type="hidden" name="collection_name" value="{{ $mediaCollectionName }}">
                <input type="hidden" name="model_type" value="{{ get_class($model) }}">
                <input type="hidden" name="model_id" value="{{ $model->id }}">
                <button type="submit" class="@mediaClass('media-manager-button-upload')">
                    {{ trans_choice('media-library-extensions::messages.upload-or-replace', $media->count()) }}
                </button>
            </form>
            @if(!$medium)
                <p class="@mediaClass('media-manager-no-media')">
                    {{ __('media-library-extensions::messages.no-medium') }}
                </p>
            @endif
        @endif

        @if($medium)
            <div class="@mediaClass('media-manager-single-preview-wrapper')">
                <a class="@mediaClass('media-manager-single-preview-medium-link')" data-bs-toggle="modal"
                   data-bs-target="#{{$modalId}}">
                    <img src="{{ $medium->getUrl() }}"
                         class="@mediaClass('media-manager-single-preview-medium')" alt=" {{ __('media-library-extensions::messages.no-medium') }}">
                </a>
                <div class="@mediaClass('media-manager-single-preview-menu')">
                    @if($destroyEnabled && !is_null($destroyRoute))
                        <form class="@mediaClass('media-manager-single-preview-form')"
                              action="{{ $destroyRoute }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="@mediaClass('media-manager-button-icon-delete')">
                                {{ __('media-library-extensions::messages.delete_medium') }}
                                {{--                            <svg width="16" height="16">--}}
                                {{--                                <use href="{{ url('/images/svg-sprites/bootstrap-icons-backend-sprite.svg#trash') }}"></use>--}}
                                {{--                            </svg>--}}
                            </button>
                        </form>
                    @endif
                </div>
                <x-media-library-extensions::preview-modal :modal-id="$modalId" :model="$model" :media-collection-name="$mediaCollectionName" title="Media carousel"/>
            </div>
        @endif
    </div>

    @if(!$uploadEnabled && !$medium)
        <span>{{ __('media-library-extensions::messages.no-medium') }}</span>
    @endif
</div>
@once
    {{--    <script src="{{ mle_package_asset('mediaPreviewModal.js') }}"></script>--}}
    <link rel="stylesheet" href="{{ mle_package_asset('media-library-extensions.css') }}">
@endonce



{{-- TODO investigate if i can use blade directive for @includeIF @mediaManagerTheme('media-manager-single')--}}
{{--        @includeIf("media-library-extensions::components.partials.media-manager-single.{$theme}", [--}}
{{--            'uploadEnabled' => $uploadEnabled,--}}
{{--            'uploadRoute' => $uploadRoute,--}}
{{--            'uploadFieldName' => $uploadFieldName,--}}
{{--            'destroyEnabled' => $destroyEnabled,--}}
{{--            'destroyRoute' => $destroyRoute,--}}
{{--            'model' => $model,--}}
{{--            'mediaCollectionName' => $mediaCollectionName,--}}
{{--            'showMediaUrl' => $showMediaUrl,--}}
{{--            'modalId' => $modalId,--}}
{{--            'title' => $title,--}}
{{--            'media' => $media,--}}
{{--            'modelKebabName' => $modelKebabName,--}}
{{--            'classes' => $classes,--}}
{{--            'theme' => $theme--}}
{{--        ])--}}
