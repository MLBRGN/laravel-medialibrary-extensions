@php use Illuminate\Support\Str; @endphp
@props([
    'uploadEnabled' => false,
    'uploadRoute' => null,
    'uploadFieldName' => 'media',
    'destroyEnabled' => false,
    'destroyRoute' => null,
    'setAsFirstInCollectionEnabled' => false,
    'setAsFirstInCollectionRoute' => null,
    'model' => null,
    'mediaCollectionName' => null,
    'showOrderIndex' => false,
    'modalId' => 'media-manager-multiple-modal',
    'title' => '',
])

<div {{$attributes->class(["media-manager media-manager-multiple"]) }}>

    @if(is_null($model))
       <p class="text-warning">No model provided!</p>
    @elseif(is_null($mediaCollectionName))
        <p class="text-warning">No mediaCollectionName provided!</p>
    @elseif(is_null($uploadRoute) && $uploadEnabled === true)
        <p class="text-warning">No upload route provided!</p>
    @elseif(is_null($destroyRoute) && $destroyEnabled === true)
        <p class="text-warning">No destroy route provided!</p>
    @elseif($setAsFirstInCollectionEnabled && is_null($setAsFirstInCollectionRoute))
        <p class="text-warning">No set-as-first-in-collection-route attribute provided!</p>
    @else
        @if(!empty($title))
            <h2 class="mb-5">{{ $title }}</h2>
        @endif
        <div class=" container-fluid">
            <div class="row media-manager-inner p-0 flex-nowrap">
                @php
                    $media = $model->getMedia($mediaCollectionName);
                    $modelKebabName = Str::kebab(class_basename($model));
                @endphp

                @if($uploadEnabled && !is_null($uploadRoute))
                    <div class="col-12 col-sm-4 media-manager-upload p-5">
                        <x-form-form class="d-flex flex-column align-items-start gap-3 mb-3" action="{{ route($uploadRoute) }}" enctype="multipart/form-data">
                            <x-form.input accept="image/jpeg,image/png,image/gif,image/bmp,image/svg+xml,image/heic,image/avif" name="{{ $uploadFieldName }}[]" type="file" multiple/>
                            <x-form.input type="hidden" name="collection_name" value="{{ $mediaCollectionName }}"/>
                            <x-form.input type="hidden" name="model_type" value="{{ get_class($model) }}"/>
                            <x-form.input type="hidden" name="model_id" value="{{ $model->id }}"/>
                            <x-form.submit class="btn-update">Upload media</x-form.submit>
                        </x-form-form>
                        @if($media->count() === 0)
                            <p class="my-3">Nog geen media geüpload</p>
                        @endif
                    </div>
                @endif

                @if($media->count() > 0)
                    {{-- Preview of all images in grid --}}
                    <div class="col-12 col-sm-8 media-manager-preview">
                        <div class="media-manager-preview-images">
                            @foreach($media as $medium)
                                <div class="media-manager-preview-image-container">
                                    <div data-bs-toggle="modal" data-bs-target="#{{$modalId}}">
                                        <a class="previewed-image cursor-zoom-in" data-bs-target="#{{$modalId}}-carousel" data-bs-slide-to="{{ $loop->index }}">
                                            {{ $medium->img()->lazy()->attributes(['class' => 'w-100 h-100 object-cover']) }}
                                        </a>
                                    </div>
                                    @if($setAsFirstInCollectionEnabled && $showOrderIndex)
                                        <span class="media-manager-order">{{ $medium->order_column }}</span>
                                    @endif
                                    <div class="media-manager-preview-image-menu d-flex justify-content-between px-2 align-items-center">
                                        <div class="media-manager-preview-image-menu-start">
                                            @if($setAsFirstInCollectionEnabled)
                                                @if($medium->order_column === 1)
                                                    <button class="btn btn-icon btn-icon-dummy btn-sm" title="Ingesteld als hoofdafbeelding" disabled>
                                                        <svg class="fill-update" width="16" height="16">
                                                            <use href="{{ url('/images/svg-sprites/bootstrap-icons-backend-sprite.svg?1#star-fill') }}"></use>
                                                        </svg>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="media-manager-preview-image-menu-end d-flex align-items-center gap-1">
                                            @if($setAsFirstInCollectionEnabled)
                                                @if($medium->order_column !== 1)
                                                    <x-form.form class="media-manager-menu-form" action="{{ route($setAsFirstInCollectionRoute) }}" method="post">
                                                        <input type="hidden" name="medium_id" value="{{ $medium->id }}">
                                                        <input type="hidden" name="collection_name" value="{{ $mediaCollectionName }}">
                                                        <input type="hidden" name="model_type" value="{{ get_class($model) }}">
                                                        <input type="hidden" name="model_id" value="{{ $model->id }}">
                                                        <button type="submit" class="btn btn-icon btn-icon-update btn-sm" title="Stel in als hoofdafbeelding">
                                                            <svg width="16" height="16">
                                                                <use href="{{ url('/images/svg-sprites/bootstrap-icons-backend-sprite.svg?1#star') }}"></use>
                                                            </svg>
                                                        </button>
                                                    </x-form.form>
                                                @endif
                                            @endif
                                            @if($destroyEnabled)
                                                <x-form.form class="media-manager-menu-form" action="{{ route($destroyRoute, $medium) }}"
                                                             method="delete">
                                                    <x-form.submit class="btn-icon btn-icon-delete btn-sm" title="Verwijder">
                                                        <svg width="16" height="16">
                                                            <use href="{{ url('/images/svg-sprites/bootstrap-icons-backend-sprite.svg#trash') }}"></use>
                                                        </svg>
                                                    </x-form.submit>
                                                </x-form.form>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                        <x-media.preview-modal :modal-id="$modalId" :model="$model" :media-collection-name="$mediaCollectionName" title="Media carousel"/>
                    </div>
                @endif
            </div>
        </div>
        @if(!$uploadEnabled && $media->count() === 0)
            <span>Geen media</span>
        @endif
    @endif
</div>
