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
        <p class="text-warning">No model provided!</p>
    @elseif(is_null($mediaCollectionName))
        <p class="text-warning">No mediaCollectionName provided!</p>
    @elseif(is_null($uploadRoute) && $uploadEnabled === true)
        <p class="text-warning">No upload route provided!</p>
    @elseif(is_null($destroyRoute) && $destroyEnabled === true)
        <p class="text-warning">No destroy route provided!</p>
    @else
        @if(!empty($title))
            <h2 class="mb-5">{{ $title }}</h2>
        @endif
        @php
            $media = $model->getMedia($mediaCollectionName);
            $modelKebabName = Str::kebab(class_basename($model));
        @endphp
        <div class=" container-fluid">
            <div class="media-manager-inner row flex-nowrap">
                @if($uploadEnabled && !is_null($uploadRoute))
                    <div class="col-12 col-sm-4 media-manager-upload p-5">
                        <x-form-form class="d-flex flex-column align-items-start gap-3 mb-3" action="{{ route($uploadRoute) }}" enctype="multipart/form-data">
                            <x-form.input class="mb-3" accept="image/jpeg,image/png,image/gif,image/bmp,image/svg+xml,image/heic,image/avif" name="{{ $uploadFieldName }}" type="file"/>
                            <x-form.input type="hidden" name="collection_name" value="{{ $mediaCollectionName }}"/>
                            <x-form.input type="hidden" name="model_type" value="{{ get_class($model) }}"/>
                            <x-form.input type="hidden" name="model_id" value="{{ $model->id }}"/>
                            <x-form.submit class="btn-update">{{ $model->getMedia($mediaCollectionName)->count() === 0 ? 'Medium uploaden' : 'Medium vervangen' }}</x-form.submit>
                        </x-form-form>
                        @if($media->count() === 0)
                            <p class="my-3">Nog geen medium geüpload</p>
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
                                    <x-form.form class="media-manager-menu-form" action="{{ route($destroyRoute, $model->getFirstMedia($mediaCollectionName)) }}" method="delete">
                                        <x-form.submit class="btn btn-icon btn-icon-delete btn-sm">
                                            <svg width="16" height="16">
                                                <use href="{{ url('/images/svg-sprites/bootstrap-icons-backend-sprite.svg#trash') }}"></use>
                                            </svg>
                                        </x-form.submit>
                                    </x-form.form>
                                @endif
                            </div>
                        </div>
                        <x-media.preview-modal :modal-id="$modalId" :model="$model" :media-collection-name="$mediaCollectionName" title="Media" single-medium/>
                    </div>
                @endif
            </div>
        </div>
        @if(!$uploadEnabled && $media->count() === 0)
            <span>Geen medium</span>
        @endif
    @endif
</div>
