@if ($temporaryUpload)
    @include('media-library-extensions::components.plain.media-carousel-temporary', [
        'media' => $media,
        'id' => $id,
        'theme' => $frontendTheme,
    ])
@else
    @include('media-library-extensions::components.plain.media-carousel-permanent', [
        'media' => $media,
        'model' => $model,
        'id' => $id,
        'frontendTheme' => $frontendTheme,
    ])
@endif