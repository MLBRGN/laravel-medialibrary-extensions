{{--This file will handle shared logic and delegate the UI to a partial.--}}
{{-- TODO investigate if i can use blade directive for @includeIF @mediaManagerTheme('media-manager-single')--}}
<div class="@mediaClass('media-manager-single-wrapper')">
        @includeIf("media-library-extensions::components.partials.media-manager-single.{$theme}", [
            'uploadEnabled' => $uploadEnabled,
            'uploadRoute' => $uploadRoute,
            'uploadFieldName' => $uploadFieldName,
            'destroyEnabled' => $destroyEnabled,
            'destroyRoute' => $destroyRoute,
            'model' => $model,
            'mediaCollectionName' => $mediaCollectionName,
            'showMediaUrl' => $showMediaUrl,
            'modalId' => $modalId,
            'title' => $title,
            'media' => $media,
            'modelKebabName' => $modelKebabName,
            'classes' => $classes,
            'theme' => $theme
        ])
</div>
