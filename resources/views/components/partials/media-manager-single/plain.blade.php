@if(!empty($title))
    <h2 class="@mediaClass('media-manager-headings')">{{ $title }}</h2>
@endif

@if($uploadEnabled && !is_null($uploadRoute))
    <form method="POST" action="{{ route(config('media-library-extensions.route-prefix').'-media-upload-single') }}" enctype="multipart/form-data" class="@mediaClass('media-manager-single-form')">
        @csrf
        <input type="file" name="{{ $uploadFieldName }}" class="@mediaClass('media-manager-input-file')">
        <input type="hidden" name="collection_name" value="{{ $mediaCollectionName }}">
        <input type="hidden" name="model_type" value="{{ get_class($model) }}">
        <input type="hidden" name="model_id" value="{{ $model->id }}">
        <button type="submit" class="@mediaClass('media-manager-button-upload')">{{ trans_choice('media-library-extensions::messages.upload-or-replace', $media->count()) }}</button>
    </form>
@endif

@if($media->count() > 0)
    <div class="@mediaClass('media-manager-single-image-preview-wrapper')">
        <img src="{{ $model->getFirstMedia($mediaCollectionName)->getUrl() }}" alt="Media" class="@mediaClass('media-manager-single-image-preview-image')">
    </div>
@endif
