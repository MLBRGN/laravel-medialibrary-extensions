<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Blog Integration Showcase</title>
        @if($theme === 'bootstrap-5')
            <link
                href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
                rel="stylesheet"
                crossorigin="anonymous"
            >
        @endif
        <style>
            html {
                scroll-behavior: auto !important;
            }
            body { font-family: sans-serif; padding: 2rem; }
            section { margin-bottom: 2rem; border: 1px solid #ccc; padding: 1rem; }
        </style>
    </head>
    <body>
    <div class="container">
        <h1>Blog Integration Showcase</h1>

        <section id="section-blog-main">
            <h2>Featured Image (Single)</h2>
            <x-mle-media-manager-single
                id="blog-main"
                :model-reference="$blog"
                :collections="['image' => 'blog-main']"
                :options="['use_xhr' => $useXhr]"
            />
        </section>

        <section id="section-blog-gallery">
            <h2>Gallery (Multiple)</h2>
            <x-mle-media-manager-multiple
                id="blog-gallery"
                :model-reference="$blog"
                :collections="['image' => 'blog-gallery']"
                :options="['use_xhr' => $useXhr]"
            />
        </section>

        <section id="section-blog-youtube">
            <h2>YouTube Video</h2>
            <x-mle-media-manager-single
                id="blog-youtube"
                :model-reference="$blog"
                :collections="['youtube' => 'blog-youtube']"
                :options="['use_xhr' => $useXhr, 'type' => 'youtube']"
            />
        </section>

        <section id="section-blog-lab">
            <h2>Media Lab</h2>
            <x-mle-media-manager-single
                id="blog-lab"
                :model-reference="$blog"
                :collections="['image' => 'blog-lab']"
                :options="['use_xhr' => $useXhr, 'enable_media_lab' => true]"
            />
        </section>
    </div>
    </body>
</html>
