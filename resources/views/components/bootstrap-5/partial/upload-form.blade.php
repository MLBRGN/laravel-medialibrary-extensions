<form
    {{ $attributes->class(['media-manager-form col-12 col-md-4']) }}
    action="{{ $multiple ? route(mle_prefix_route('media-upload-multiple')) : route(mle_prefix_route('media-upload-single')) }}"
    
    enctype="multipart/form-data"
    method="post">
    @csrf
    @if($multiple)
        <input
            accept="{{ $allowedMimeTypes }}"
            name="{{ config('media-library-extensions.upload_field_name_multiple') }}[]"
            type="file"
            class="media-manager-input-file form-control"
            multiple/>
    @else
        <input
            accept="{{ $allowedMimeTypes }}"
            name="{{ config('media-library-extensions.upload_field_name_single') }}"
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
        class="btn btn-primary">
        {{ $multiple
         ? __('media-library-extensions::messages.upload_media')
         : trans_choice('media-library-extensions::messages.upload_or_replace', $mediaPresent ? 1 : 0) }}
    </button>
    <x-mle-partial-flash :target-id="$id"/>
</form>
