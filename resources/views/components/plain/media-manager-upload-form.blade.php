@php use Mlbrgn\LaravelFormComponents\Tests\Feature\Models\ActivityModel; @endphp
@props([
    'uploadRoute',
    'allowedMimeTypes',
    'uploadFieldName',
    'mediaCollection',
    'model',
    'id'
])

<form
    {{ $attributes->class(['media-manager-form col-12 col-md-4']) }}
    action="{{ $uploadRoute }}"
    enctype="multipart/form-data"
    method="post">
    @csrf
    <input
        accept="{{ $allowedMimeTypes }}"
        name="{{ $uploadFieldName }}[]"
        type="file"
        class="media-manager-input-file form-control"
        multiple/>
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
        {{ __('media-library-extensions::messages.upload-media') }}
    </button>
    <x-mle_internal-flash :target-id="$id"/>
</form>
