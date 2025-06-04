@php use App\Models\Blog; @endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Media Library Extensions Test Page</title>
        <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app.css') }}">
        <script src="{{ asset('vendor/media-library-extensions/app.js') }}"></script>
    </head>
    <body>
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
                frontend-theme="plain"
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
                frontend-theme="plain"
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
                frontend-theme="plain"
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
                frontend-theme="plain"
                />
        </div>
    </body>
</html>
