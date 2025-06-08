@php use App\Models\Blog; @endphp
@php
   config(['media-library-extensions.frontend_theme' => 'plain']);
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Media Library Extensions Test Page</title>
{{--        <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app.css') }}">--}}
{{--        <script src="{{ asset('vendor/media-library-extensions/app.js') }}"></script>--}}
    </head>
    <body>
        <div>
            <h1 class="text-primary">Laravel Media Library Extensions Component Test Page (Plain theme)</h1>
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
                :media-collections="['blog-main', 'blog-images', 'blog-images-extra']"
                class="mt-5"
                frontend-theme="plain"
                />
        </div>
    </body>
</html>
