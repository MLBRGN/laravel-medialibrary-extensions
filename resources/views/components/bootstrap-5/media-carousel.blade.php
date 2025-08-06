@if ($temporaryUpload)
    @include('media-library-extensions::components.bootstrap-5.media-carousel-temporary', [
        'media' => $media,
        'modelType' => $modelType,
        'modelId' => null,
        'id' => $id,
        'theme' => $frontendTheme,
    ])
@else
    @include('media-library-extensions::components.bootstrap-5.media-carousel-permanent', [
        'media' => $media,
//        'modelType' => $modelType,
//        'modelId' => null,
        'model' => $model,
        'id' => $id,
        'frontendTheme' => $frontendTheme,
    ])
@endif