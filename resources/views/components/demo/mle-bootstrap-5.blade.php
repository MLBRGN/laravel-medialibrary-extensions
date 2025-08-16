<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Component tests: theme bootstrap-5</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        {{--            integrity="sha384-QWTKZyjpPEjISv5WaRU5M6QdFVb2l9gCk0GZg6CJWjvvoE5yOAy+n9C80+XW9HdT"--}}
        crossorigin="anonymous"
    >
</head>
<body>
<div class="container-lg mt-5">
    <h1 class="text-primary">Component tests: theme bootstrap-5</h1>
    <div>
        <h2 class="my-5">Media Manager Single</h2>

        <x-mle-media-manager-single
            id="alien-single"
            :model-or-class-name="$model"
            image-collection="alien-single-image"
            document-collection="alien-single-document"
            youtube-collection="alien-single-youtube-video"
            video-collection="alien-single-video"
            audio-collection="alien-single-audio"
            class="mt-5"
            upload-enabled
            destroy-enabled
            frontend-theme="bootstrap-5"
            :use-xhr="config('media-library-extensions.use_xhr')"
        />

        <h2 class="my-5">Media Manager Single (Temporary uploads)</h2>

        <x-mle-media-manager-single
            id="aliens-single-temporary-uploads"
            model-or-class-name="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien"
            image-collection="alien-single-image"
            document-collection="alien-single-document"
            youtube-collection="alien-single-youtube-video"
            video-collection="alien-single-video"
            audio-collection="alien-single-audio"
            class="mt-5"
            upload-enabled
            destroy-enabled
            frontend-theme="bootstrap-5"
            :use-xhr="config('media-library-extensions.use_xhr')"
        />
        
        <h2 class="my-5">Media Manager Multiple</h2>

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
            :show-order="true"
            set-as-first-enabled
            frontend-theme="bootstrap-5"
            :use-xhr="config('media-library-extensions.use_xhr')"
            :multiple="true"
            :show-menu="false"
        />

        <h2 class="my-5">Media Manager Multiple (Temporary uploads)</h2>

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
            :show-order="true"
            set-as-first-enabled
            frontend-theme="bootstrap-5"
            :use-xhr="config('media-library-extensions.use_xhr')"
            :multiple="true"
        />

        <h2 class="my-5">Media Manager YouTube</h2>

        <x-mle-media-manager-youtube
            id="alien-media-manager-youtube"
            :model-or-class-name="$model"
            youtube-collection="alien-multiple-youtube-videos"
            class="mt-5"
            upload-enabled
            destroy-enabled
            :show-order="true"
            set-as-first-enabled
            frontend-theme="bootstrap-5"
            :use-xhr="config('media-library-extensions.use_xhr')"
            :multiple="true"
        />

        <h2 class="my-5">Media Manager YouTube (Temporary uploads)</h2>

        <x-mle-media-manager-youtube
            id="alien-media-manager-youtube-temporary"
            model-or-class-name="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien"
            youtube-collection="alien-multiple-youtube-videos"
            class="mt-5"
            upload-enabled
            destroy-enabled
            :show-order="true"
            set-as-first-enabled
            frontend-theme="bootstrap-5"
            :use-xhr="config('media-library-extensions.use_xhr')"
            :multiple="true"
        />

        <h2 class="my-5">Media Carousel</h2>

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
            class="my-5"
            frontend-theme="bootstrap-5"
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
            frontend-theme="bootstrap-5"
        />

        <h2 class="my-5">Youtube video</h2>
        
{{--        <x-mle-youtube-video :model-or-class-name="$model" collection="alien-single-youtube-video" :preview="false"/>--}}

        <h2 class="my-5">Youtube video temporary</h2>
        
{{--        <x-mle-youtube-video model-or-class-name="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien" collection="alien-single-youtube-video" :preview="false"/>--}}

        <h2 class="my-5">Media first available</h2>

        <x-mle-first-available 
            id="media-first-available"
            :model-or-class-name="$model" 
            :media-collections="['alien-single-audio', 'alien-single-video', 'alien-single-document', 'alien-single-image', 'alien-single-youtube-video']"
        />

    </div>
</div>
{{-- Bootstrap 5 JS Bundle with Popper --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        {{--                integrity="sha384-SrVJJmZaeJbk2nXpcoZ8jP+gNcTo6MSuEiwF5Bd9TIUO6Up9qX3YqZJXfKh1WTRi" --}}
        crossorigin="anonymous"></script>
@once
    <script type="module" src="{{ asset('vendor/media-library-extensions/demo.js') }}"></script>
@endonce
</body>
</html>
