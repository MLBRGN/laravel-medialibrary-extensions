@php use App\Models\Blog; @endphp
@php
   config(['media-library-extensions.frontend_theme' => 'plain']);
@endphp
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
           margin-block:1.5em;
        }
        @media (min-width: 1400px) {
           .mle-container-lg {
               max-width: 1320px;
               margin-right: auto;
               margin-left: auto;
           }
        }
    </style>
{{--    <link rel="icon" type="image/png" href="/favicon.icon" />--}}

    </head>
    <body>
        <div class="mle-container-lg">
            <h1 class="text-primary">Component tests: theme plain</h1>
            @php            
                // $model that implements HasMedia
                $model = Blog::first();
            @endphp
          
            <h2>Media Manager Single</h2>
          
            <x-mle-media-manager-single
                id="blog-main"
                :model="$model"
                media-collection="blog-main"
                document-collection="blog-main-documents"
                class="mt-5"
                upload-enabled
                destroy-enabled
                frontend-theme="plain"
                :use-xhr="config('media-library-extensions.use_xhr')"
                />

            <h2>Media Manager Multiple</h2>
          
            <x-mle-media-manager-multiple
                id="blog-images"
                :model="$model"
                media-collection="blog-images"
                youtube-collection="blog-youtube-videos"
                document-collection="blog-documents"
                class="mt-5"
                upload-enabled
                destroy-enabled
                show-order
                set-as-first-enabled
                frontend-theme="bootstrap-5"
                :use-xhr="config('media-library-extensions.use_xhr')"
                />
        
            <h2>Media Carousel</h2>

            <x-mle-media-carousel
                id="blog-media"
                :model="$model"
                :media-collections="['blog-images', 'blog-documents', 'blog-youtube-videos']"
                class="mt-5"
                frontend-theme="plain"
                />
        </div>
    </body>
</html>
