@if ($temporaryUpload)
    @include('media-library-extensions::components.plain.media-carousel-temporary', [
        'media' => $media,
        'destroyEnabled' => $destroyEnabled,
        'id' => $id,
        'theme' => $frontendTheme,
    ])
@else
    @include('media-library-extensions::components.plain.media-carousel-permanent', [
        'media' => $media,
        'model' => $model,
        'destroyEnabled' => $destroyEnabled,
        'setAsFirstEnabled' => $setAsFirstEnabled,
        'showOrder' => $showOrder,
        'showMenu' => true,
        'imageCollection' => $imageCollection,
        'documentCollection' => $documentCollection,
        'youtubeCollection' => $youtubeCollection,
        'id' => $id,
        'frontendTheme' => $frontendTheme,
    ])
@endif