<x-blogs-layout :theme="$theme" :title="'Blog: ' . $blog->title">
    <h1 class="mb-5">Blog: {{ $blog->title }}</h1>
    <a href="{{ route('blogs.index', ['theme' => $theme, 'use_xhr' => $useXhr ? 1 : 0]) }}" class="btn btn-secondary mb-5">Back to blogs</a>

    <div class="mb-5">
        <strong>Content:</strong>
        <div>{{ $blog->content }}</div>
    </div>

    <div class="mb-5">
        <h2>Featured Image</h2>
        <x-mle-media-manager-single
            id="blog-main-show"
            :model-reference="$blog"
            :collections="['image' => 'blog-main']"
            :options="['use_xhr' => $useXhr, 'theme' => $theme]"
            :readonly="true"
        />
    </div>

    <div class="mb-5">
        <h2>Gallery</h2>
        <x-mle-media-manager-multiple
            id="blog-gallery-show"
            :model-reference="$blog"
            :collections="['image' => 'blog-gallery']"
            :options="['use_xhr' => $useXhr, 'theme' => $theme]"
            :readonly="true"
        />
    </div>

    <div class="mb-5">
        <h2>Carousel Preview</h2>
        <x-mle-media-carousel
            id="blog-carousel-show"
            :model-reference="$blog"
            :collections="['image' => ['blog-main', 'blog-gallery']]"
            :options="['theme' => $theme]"
        />
    </div>

    <div class="mt-5">
        <a href="{{ route('blogs.edit', ['blog' => $blog, 'theme' => $theme, 'use_xhr' => $useXhr ? 1 : 0]) }}" class="btn btn-warning">Edit</a>
    </div>
</x-blogs-layout>
