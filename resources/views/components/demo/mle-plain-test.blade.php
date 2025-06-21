<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Component tests: theme plain</title>
    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }

        h2 {
            margin-block: 1.5em;
        }
        
        .demo-media-carousel {
            margin-block: 3rem;
        }

        @media (min-width: 1400px) {
            .mle-container-lg {
                max-width: 1320px;
                margin-right: auto;
                margin-left: auto;
            }
        }
    </style>
</head>
<body>
<div class="mle-container-lg">
    <h1 class="text-primary">Component tests: theme plain</h1>

    <h2>Media Manager Single</h2>

{{--    <x-mle-media-manager--}}
{{--        id="blog-main"--}}
{{--        :model="$model"--}}
{{--        media-collection="alien-single-image"--}}
{{--        document-collection="alien-single-document"--}}
{{--        youtube-collection="alien-single-youtube-video"--}}
{{--        class="mt-5"--}}
{{--        upload-enabled--}}
{{--        destroy-enabled--}}
{{--        frontend-theme="plain"--}}
{{--        :use-xhr="config('media-library-extensions.use_xhr')"--}}
{{--        :multiple="false"--}}
{{--    />--}}
    
    <x-mle-media-manager-single
        id="blog-main"
        :model="$model"
        media-collection="alien-single-image"
        document-collection="alien-single-document"
        youtube-collection="alien-single-youtube-video"
        upload-enabled
        destroy-enabled
        frontend-theme="plain"
        :use-xhr="config('media-library-extensions.use_xhr')"
    />

    <h2>Media Manager Multiple</h2>

{{--    <x-mle-media-manager--}}
{{--        id="blog-images"--}}
{{--        :model="$model"--}}
{{--        media-collection="alien-multiple-images"--}}
{{--        document-collection="alien-multiple-documents"--}}
{{--        youtube-collection="alien-multiple-youtube-videos"--}}
{{--        class="mt-5"--}}
{{--        upload-enabled--}}
{{--        destroy-enabled--}}
{{--        :show-order="true"--}}
{{--        set-as-first-enabled--}}
{{--        frontend-theme="plain"--}}
{{--        :use-xhr="config('media-library-extensions.use_xhr')"--}}
{{--        :multiple="true"--}}
{{--    />--}}
    <x-mle-media-manager-multiple
        id="blog-images"
        :model="$model"
        media-collection="alien-multiple-images"
        document-collection="alien-multiple-documents"
        youtube-collection="alien-multiple-youtube-videos"
        class="mt-5"
        upload-enabled
        destroy-enabled
        show-order
        set-as-first-enabled
        frontend-theme="plain"
        :use-xhr="config('media-library-extensions.use_xhr')"
        :multiple="true"
    />

    <h2>Media Carousel</h2>

    <p>{{ __('media-library-extensions::messages.note_carousel_only_updates_on_refresh_of_page') }}</p>

    <x-mle-media-carousel
        id="blog-media"
        :model="$model"
        :media-collections="[
                    'alien-single-image', 
                    'alien-single-document', 
                    'alien-single-youtube-video',
                    'alien-multiple-images', 
                    'alien-multiple-documents', 
                    'alien-multiple-youtube-videos'
                ]"
        class="demo-media-carousel"
        frontend-theme="plain"
    />
</div>
</body>
</html>
