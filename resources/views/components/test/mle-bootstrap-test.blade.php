@php use App\Models\Blog; @endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Media Library Extensions Test Page</title>
        {{-- Bootstrap 5.3 CSS CDN --}}
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
{{--            integrity="sha384-QWTKZyjpPEjISv5WaRU5M6QdFVb2l9gCk0GZg6CJWjvvoE5yOAy+n9C80+XW9HdT"--}}
            crossorigin="anonymous"
        >
    </head>
    <body>
        <div class="container-lg mt-5">
            <h1 class="text-primary">Laravel Media Library Extensions Component Test Page (Bootstrap 5 theme)</h1>
       
            <div>
                @php            
                    // $model that implements HasMedia
                    $model = Blog::first();
                @endphp
                <x-mle-media-manager-single
                    id="blog-main"
                    :model="$model"
                    media-collection="blog-main"
                    class="mt-5"
                    upload-enabled
                    destroy-enabled
                    frontend-theme="bootstrap-5"
                    />
            
                <x-mle-media-manager-multiple
                    id="blog-images"
                    :model="$model"
                    media-collection="blog-images"
                    class="mt-5"
                    upload-enabled
                    destroy-enabled
                    show-order
                    set-as-first-enabled
                    frontend-theme="bootstrap-5"
                    />
            
                <x-mle-media-manager-multiple
                    id="blog-images-extra"
                    :model="$model"
                    media-collection="blog-images-extra"
                    class="mt-5"
                    upload-enabled
                    destroy-enabled
                    show-order
                    set-as-first-enabled
                    frontend-theme="bootstrap-5"
                    />
            
                <h1>Blog media carousel</h1>
                <x-mle-media-carousel
                    id="blog-media"
                    :model="$model"
                    {{--        media-collection="blog-images"--}}
                    :media-collections="['blog-main', 'blog-images', 'blog-images-extra']"
                    class="mt-5"
                    upload-enabled
                    destroy-enabled
                    show-order
                    set-as-first-enabled
                    frontend-theme="bootstrap-5"
                    />
            </div>
        </div>
        {{-- Bootstrap 5 JS Bundle with Popper --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
{{--                integrity="sha384-SrVJJmZaeJbk2nXpcoZ8jP+gNcTo6MSuEiwF5Bd9TIUO6Up9qX3YqZJXfKh1WTRi" --}}
                crossorigin="anonymous"></script>
    </body>
</html>
