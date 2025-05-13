{{--@if(!empty($title))--}}
{{--    <h2 class="@mediaClass('media-manager-headings')">{{ $title }}</h2>--}}
{{--@endif--}}

{{--<div class="@mediaClass('media-manager-single-row')">--}}
{{--    @if($uploadEnabled && !is_null($uploadRoute))--}}
{{--        <form method="POST" action="{{ route(config('media-library-extensions.route-prefix').'-media-upload-single') }}" enctype="multipart/form-data" class="@mediaClass('media-manager-single-form')">--}}
{{--            @csrf--}}
{{--            <input type="file" name="{{ $uploadFieldName }}" class="@mediaClass('media-manager-input-file')">--}}
{{--            <input type="hidden" name="collection_name" value="{{ $mediaCollectionName }}">--}}
{{--            <input type="hidden" name="model_type" value="{{ get_class($model) }}">--}}
{{--            <input type="hidden" name="model_id" value="{{ $model->id }}">--}}
{{--            <button type="submit" class="@mediaClass('media-manager-button-upload')">--}}
{{--                {{ trans_choice('media-library-extensions::messages.upload-or-replace', $media->count()) }}--}}
{{--            </button>--}}
{{--        </form>--}}
{{--        @if($media->count() === 0)--}}
{{--            <p class="my-3">Nog geen medium geüpload</p>--}}
{{--        @endif--}}
{{--    @endif--}}

{{--    @if($media->count() > 0)--}}
{{--        <div class="@mediaClass('media-manager-single-preview-wrapper')">--}}
{{--            <a class="previewed-image cursor-zoom-in" data-bs-toggle="modal" data-bs-target="#{{$modalId}}">--}}
{{--                <img src="{{ $model->getFirstMedia($mediaCollectionName)->getUrl() }}" alt="Media" class="@mediaClass('media-manager-single-preview-medium')">--}}
{{--            </a>--}}
{{--            <div class="media-manager-preview-image-menu d-flex justify-content-end px-2 align-items-center">--}}
{{--                @if($destroyEnabled && !is_null($destroyRoute))--}}
{{--                    <form class="media-manager-menu-form" action="{{ route(config('media-library-extensions.route-prefix').'-media-destroy', $model->getFirstMedia($mediaCollectionName)) }}" method="post">--}}
{{--                        @csrf--}}
{{--                        @method('DELETE')--}}
{{--                        <button type="submit" class="@mediaClass('media-manager-button-icon-delete')">--}}
{{--                            {{ __('media-library-extensions::messages.delete_medium') }}--}}
{{--                            --}}{{--                            <svg width="16" height="16">--}}
{{--                            --}}{{--                                <use href="{{ url('/images/svg-sprites/bootstrap-icons-backend-sprite.svg#trash') }}"></use>--}}
{{--                            --}}{{--                            </svg>--}}
{{--                        </button>--}}
{{--                    </form>--}}
{{--                @endif--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    @endif--}}
{{--</div>--}}

{{--@if(!$uploadEnabled && $media->count() === 0)--}}
{{--    <span>Geen medium</span>--}}
{{--@endif--}}
