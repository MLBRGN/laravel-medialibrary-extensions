@if ($temporaryUploads)
    @include('media-library-extensions::components.bootstrap-5.media-manager-preview-temporary', [
        'media' => $media,
        'modelType' => $modelType,
        'modelId' => null,
        'destroyEnabled' => $destroyEnabled,
        'setAsFirstEnabled' => $setAsFirstEnabled,
        'showOrder' => $showOrder,
        'showMenu' => true,
        'imageCollection' => $imageCollection,
        'documentCollection' => $documentCollection,
        'youtubeCollection' => $youtubeCollection,
        'id' => $id,
        'theme' => $frontendTheme,
    ])
@else
    @include('media-library-extensions::components.bootstrap-5.media-manager-preview-permanent', [
        'media' => $media,
//        'modelType' => $modelType,
//        'modelId' => null,
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