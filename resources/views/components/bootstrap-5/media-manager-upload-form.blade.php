<form
    {{ $attributes->class(['media-manager-form col-12 col-md-4']) }}
    action="{{ $uploadRoute }}"
    enctype="multipart/form-data"
    method="post">
    @csrf
    @if($multiple)
        <input
            accept="{{ $allowedMimeTypes }}"
            name="{{ $uploadFieldName }}[]"
            type="file"
            class="media-manager-input-file form-control"
            multiple/>
    @else
        <input
            accept="{{ $allowedMimeTypes }}"
            name="{{ $uploadFieldName }}"
            type="file"
            class="media-manager-input-file form-control"/>
    @endif
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
        class="btn btn-success">
        {{ $multiple
         ? __('media-library-extensions::messages.upload-media')
         : trans_choice('media-library-extensions::messages.upload-or-replace', $mediaPresent ? 1 : 0) }}
    </button>
    <x-mle_internal-flash :target-id="$id"/>
</form>
