<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Component tests: theme bootstrap-5</title>
        <link rel="icon" type="image/x-icon" href="{{ route('mle.favicon') }}">
        {{--    <link--}}
        {{--        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"--}}
        {{--        rel="stylesheet"--}}
        {{--                    integrity="sha384-QWTKZyjpPEjISv5WaRU5M6QdFVb2l9gCk0GZg6CJWjvvoE5yOAy+n9C80+XW9HdT"--}}
        {{--        crossorigin="anonymous"--}}
        {{--    >--}}
    </head>
    <body>
        {{--@dump($modelType)--}}
        {{--@dump($imageCollection)--}}
        <div class="mlbrgn-mle-component media-manager-tinymce">
            @if($temporaryUpload)
                <x-mle-media-manager-multiple
                    :model-or-class-name="$modelType"
                    id="{{ $id }}"
                    :image-collection="$imageCollection"
                    :document-collection="$documentCollection"
                    :youtube-collection="$youtubeCollection"
                    :video-collection="$videoCollection"
                    :audio-collection="$audioCollection"
                    :frontend-theme="$frontendTheme"
                    :destroy-enabled="true"
                    :set-as-first-enabled="true"
                    :show-order="true"
                    :show-menu="true"
                    :multiple="true"
                    :upload-enabled="true"
                />
            @else
                <x-mle-media-manager-multiple
                    :model-or-class-name="$modelType"
                    id="{{ $id }}"
                    :image-collection="$imageCollection"
                    :document-collection="$documentCollection"
                    :youtube-collection="$youtubeCollection"
                    :video-collection="$videoCollection"
                    :audio-collection="$audioCollection"
                    :frontend-theme="$frontendTheme"
                    :destroy-enabled="true"
                    :set-as-first-enabled="true"
                    :show-order="true"
                    :show-menu="true"
                    :upload-enabled="true"
                />
            @endif
        </div>
    </body>
</html>