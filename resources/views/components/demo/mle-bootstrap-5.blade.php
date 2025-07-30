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

{{--        <label>--}}
{{--            <input type="checkbox" id="upload-mode-toggle" data-temporary-upload-toggle data-for-media-manager="blog-main-media-manager-single"/>--}}
{{--            Use Temporary Uploads--}}
{{--        </label>--}}

        <x-mle-media-manager-single
            id="blog-main"
            :model-or-class-name="$model"
            image-collection="alien-single-image"
            document-collection="alien-single-document"
            youtube-collection="alien-single-youtube-video"
            class="mt-5"
            upload-enabled
            destroy-enabled
            frontend-theme="bootstrap-5"
            :use-xhr="config('media-library-extensions.use_xhr')"
        />

        <h2 class="my-5">Media Manager Single (Temporary uploads)</h2>

        {{--        <label>--}}
        {{--            <input type="checkbox" id="upload-mode-toggle" data-temporary-upload-toggle data-for-media-manager="blog-main-media-manager-single"/>--}}
        {{--            Use Temporary Uploads--}}
        {{--        </label>--}}

        <x-mle-media-manager-single
            id="blog-main"
            model-or-class-name="\App\Models\Aliens"
            image-collection="alien-single-image"
            document-collection="alien-single-document"
            youtube-collection="alien-single-youtube-video"
            class="mt-5"
            upload-enabled
            destroy-enabled
            frontend-theme="bootstrap-5"
            :use-xhr="config('media-library-extensions.use_xhr')"
        />
        
        <h2 class="my-5">Media Manager Multiple</h2>

{{--        <label>--}}
{{--            <input type="checkbox" id="upload-mode-toggle" data-temporary-upload-toggle data-for-media-manager="blog-images-media-manager-multiple"/>--}}
{{--            Use Temporary Uploads--}}
{{--        </label>--}}

        <x-mle-media-manager-multiple
            id="blog-images"
            :model-or-class-name="$model"
            image-collection="alien-multiple-images"
            document-collection="alien-multiple-documents"
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

        <h2 class="my-5">Media Manager Multiple (Temporary uploads)</h2>

        {{--        <label>--}}
        {{--            <input type="checkbox" id="upload-mode-toggle" data-temporary-upload-toggle data-for-media-manager="blog-images-media-manager-multiple"/>--}}
        {{--            Use Temporary Uploads--}}
        {{--        </label>--}}

        <x-mle-media-manager-multiple
            id="blog-images"
            model-or-class-name="\App\Models\Aliens"
            image-collection="alien-multiple-images"
            document-collection="alien-multiple-documents"
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
            class="my-5"
            frontend-theme="bootstrap-5"
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
