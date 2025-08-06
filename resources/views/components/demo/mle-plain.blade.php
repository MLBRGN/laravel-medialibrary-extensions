<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Component tests: theme plain</title>
    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }

        h1, h2, h3, h4, h5, h6 {
            margin-top: 0;
            margin-bottom: .5rem;
            font-weight: 500;
            line-height: 1.2;
        }
        h1 {
            color:#0d6efd;
            font-size: 2.5rem;
        }
        h2 {
            margin-block: 1.5em;
            font-size: 2rem;
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
    
    <x-mle-media-manager-single
        id="blog-main"
        :model-or-class-name="$model"
        image-collection="alien-single-image"
        document-collection="alien-single-document"
        youtube-collection="alien-single-youtube-video"
        upload-enabled
        destroy-enabled
        frontend-theme="plain"
        :use-xhr="config('media-library-extensions.use_xhr')"
    />

    <h2>Media Manager Single (Temporary uploads)</h2>

    <x-mle-media-manager-single
        id="blog-main-temporary-uploads"
        model-or-class-name="\App\Models\Aliens"
        image-collection="alien-single-image"
        document-collection="alien-single-document"
        youtube-collection="alien-single-youtube-video"
        upload-enabled
        destroy-enabled
        frontend-theme="plain"
        :use-xhr="config('media-library-extensions.use_xhr')"
    />

    <h2>Media Manager Multiple</h2>
    
    <x-mle-media-manager-multiple
        id="blog-images"
        :model-or-class-name="$model"
        image-collection="alien-multiple-images"
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

    <h2>Media Manager Multiple (Temporary uploads)</h2>

    <x-mle-media-manager-multiple
        id="blog-images-temporary-uploads"
        model-or-class-name="\App\Models\Aliens"
        image-collection="alien-multiple-images"
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
        :model-or-class-name="$model"
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
@once
    <script type="module" src="{{ asset('vendor/media-library-extensions/demo.js') }}"></script>
@endonce
</body>
</html>
