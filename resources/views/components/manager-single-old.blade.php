@php use Illuminate\Support\Str; @endphp
@props([
    'uploadEnabled' => false,
    'uploadRoute' => null,
    'uploadFieldName' => 'medium',
    'destroyEnabled' => false,
    'destroyRoute' => null,
    'model' => null,
    'mediaCollectionName' => null,
    'showMediaUrl' => false,
    'modalId' => 'media-manager-single-modal',
    'title' => '',
])
<div {{$attributes->class(["media-manager media-manager-single"]) }}>
    @if(is_null($model))
        <p class="text-warning">{{ __('media-library-extensions::messages.no-model-provided') }}</p>
    @elseif(is_null($mediaCollectionName))
        <p class="text-warning">{{ __('media-library-extensions::messages.no-collection-name-provided') }}</p>
    @else
        @if(!empty($title))
            <x-media-library-extensions::title :title="$title"/>
        @endif
        @php
            $media = $model->getMedia($mediaCollectionName);
            $modelKebabName = Str::kebab(class_basename($model));
        @endphp
        <div class="container-fluid">
            <div class="media-manager-inner row flex-nowrap">
                @if($uploadEnabled && !is_null($uploadRoute))
                    <div class="col-12 col-sm-4 media-manager-upload p-5">
                        <x-form-form class="d-flex flex-column align-items-start gap-3 mb-3" action="{{ route(config('media-library-extensions.route-prefix').'-media-upload-single') }}" enctype="multipart/form-data">
                            <x-form.input class="mb-3" accept="image/jpeg,image/png,image/gif,image/bmp,image/svg+xml,image/heic,image/avif" name="{{ $uploadFieldName }}" type="file"/>
                            <x-form.input type="hidden" name="collection_name" value="{{ $mediaCollectionName }}"/>
                            <x-form.input type="hidden" name="model_type" value="{{ get_class($model) }}"/>
                            <x-form.input type="hidden" name="model_id" value="{{ $model->id }}"/>
                            <x-form.submit class="btn-update">{{  trans_choice('media-library-extensions::messages.upload-or-replace', $model->getMedia($mediaCollectionName)->count() ) }}</x-form.submit>
                        </x-form-form>
                        @if($media->count() === 0)
                            <p class="my-3">{{ __('media-library-extensions::messages.no-medium-uploaded') }}</p>
                        @endif
                    </div>
                @endif
                @if($media->count() > 0)
                    <div class="col-12 col-sm-8 media-manager-preview px-0">
                        <div class="media-preview-image d-flex justify-content-center">
                            <a class="previewed-image cursor-zoom-in" data-bs-toggle="modal" data-bs-target="#{{$modalId}}">
                                {{ $model->getFirstMedia($mediaCollectionName)->img()->attributes(['class' => 'show-enterprise-logo']) }}
                            </a>

                            <div class="media-manager-preview-image-menu d-flex justify-content-end px-2 align-items-center">
                                @if($destroyEnabled && !is_null($destroyRoute))
                                    <x-form.form class="media-manager-menu-form" action="{{ route(config('media-library-extensions.route-prefix').'-media-destroy', $model->getFirstMedia($mediaCollectionName)) }}" method="delete">
                                        <x-form.submit class="btn btn-icon btn-icon-delete btn-sm">
                                            <svg width="16" height="16">
                                                <use href="{{ url('/images/svg-sprites/bootstrap-icons-backend-sprite.svg#trash') }}"></use>
                                            </svg>
                                        </x-form.submit>
                                    </x-form.form>
                                @endif
                            </div>
                        </div>
                        <x-media-library-extensions::preview-modal :modal-id="$modalId" :model="$model" :media-collection-name="$mediaCollectionName" title="Media" single-medium/>
                    </div>
                @endif
            </div>
        </div>
        @if(!$uploadEnabled && $media->count() === 0)
            <span>{{ __('media-library-extensions::messages.no-medium') }}m</span>
        @endif
    @endif
</div>
