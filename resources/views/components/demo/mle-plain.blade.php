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
    <link rel="icon" type="image/x-icon" href="{{ route('mle.favicon') }}">
</head>
<body>
<div class="mle-container-lg">
    <h1 class="text-primary">Component tests: theme plain</h1>

    <h2>Media Manager Single</h2>
    
    <x-mle-media-manager-single
        id="alien-sinlge"
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
        id="aliens-single-temporary-uploads"
        model-or-class-name="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien"
        image-collection="alien-single-image"
        document-collection="alien-single-document"
        youtube-collection="alien-single-youtube-video"
        video-collection="alien-single-video"
        audio-collection="alien-single-audio"
        upload-enabled
        destroy-enabled
        frontend-theme="plain"
        :use-xhr="config('media-library-extensions.use_xhr')"
    />

    <h2>Media Manager Multiple</h2>
    
    <x-mle-media-manager-multiple
        id="alien-multiple"
        :model-or-class-name="$model"
        image-collection="alien-multiple-images"
        document-collection="alien-multiple-documents"
        youtube-collection="alien-multiple-youtube-videos"
        video-collection="alien-multiple-videos"
        audio-collection="alien-multiple-audio"
        class="mt-5"
        upload-enabled
        destroy-enabled
        show-order
        set-as-first-enabled
        frontend-theme="plain"
        :use-xhr="config('media-library-extensions.use_xhr')"
    />

    <h2>Media Manager Multiple (Temporary uploads)</h2>

    <x-mle-media-manager-multiple
        id="alien-multiple-temporary-uploads"
        model-or-class-name="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien"
        image-collection="alien-multiple-images"
        document-collection="alien-multiple-documents"
        youtube-collection="alien-multiple-youtube-videos"
        video-collection="alien-multiple-videos"
        audio-collection="alien-multiple-audio"
        class="mt-5"
        upload-enabled
        destroy-enabled
        show-order
        set-as-first-enabled
        frontend-theme="plain"
        :use-xhr="config('media-library-extensions.use_xhr')"
    />

    <h2 class="my-5">Media Manager YouTube</h2>

    <x-mle-media-manager-multiple
        id="alien-media-manager-youtube"
        :model-or-class-name="$model"
        youtube-collection="alien-multiple-youtube-videos"
        class="mt-5"
        upload-enabled
        destroy-enabled
        :show-order="true"
        set-as-first-enabled
        frontend-theme="plain"
        :use-xhr="config('media-library-extensions.use_xhr')"
    />

    <h2 class="my-5">Media Manager YouTube (Temporary uploads)</h2>

    <x-mle-media-manager-multiple
        id="alien-media-manager-youtube-temporary"
        model-or-class-name="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien"
        youtube-collection="alien-multiple-youtube-videos"
        class="mt-5"
        upload-enabled
        destroy-enabled
        :show-order="true"
        set-as-first-enabled
        frontend-theme="plain"
        :use-xhr="config('media-library-extensions.use_xhr')"
    />
    
    <h2>Media Carousel</h2>

    <p>{{ __('media-library-extensions::messages.note_carousel_only_updates_on_refresh_of_page') }}</p>

    <x-mle-media-carousel
        id="alien-media-carousel"
        :model-or-class-name="$model"
        :media-collections="[
                    'alien-single-image', 
                    'alien-single-document', 
                    'alien-single-youtube-video',
                    'alien-single-video',
                    'alien-single-audio',
                    'alien-multiple-images', 
                    'alien-multiple-documents', 
                    'alien-multiple-youtube-videos',
                    'alien-multiple-videos',
                    'alien-multiple-audio',
                ]"
        class="demo-media-carousel"
        frontend-theme="plain"
    />

    <h2 class="my-5">Media Carousel (Temporary)</h2>

    <p>{{ __('media-library-extensions::messages.note_carousel_only_updates_on_refresh_of_page') }}</p>

    <x-mle-media-carousel
        id="alien-media-carousel-temporary-uploads"
        model-or-class-name="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien"
        :media-collections="[
                        'alien-single-image', 
                        'alien-single-document', 
                        'alien-single-youtube-video',
                        'alien-single-video',
                        'alien-single-audio',
                        'alien-multiple-images', 
                        'alien-multiple-documents', 
                        'alien-multiple-youtube-videos',
                        'alien-multiple-videos',
                        'alien-multiple-audio',
                    ]"
        class="my-5"
        frontend-theme="plain"
    />

    <h2 class="my-5">Media first available</h2>

    <x-mle-first-available
        id="media-first-available"
        :model-or-class-name="$model"
        :media-collections="['alien-single-audio', 'alien-single-video', 'alien-single-document', 'alien-single-image', 'alien-single-youtube-video']"
    />

</div>
@once
    <script type="module" src="{{ asset('vendor/media-library-extensions/demo.js') }}"></script>
@endonce
</body>
</html>
