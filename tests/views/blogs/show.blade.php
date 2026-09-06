<x-blogs-layout :theme="$theme" :title="'Blog: ' . $blog->title">
    <h1 class="mb-5">Blog: {{ $blog->title }}</h1>
    <div class="mle-test-nav">
        <a href="{{ route('blogs.index', ['theme' => $theme, 'use_xhr' => $useXhr ? 1 : 0]) }}" class="mle-test-btn">Back to blogs</a>
    </div>

    <div class="mle-test-card">
        <div class="mle-test-form-group">
            <label class="mle-test-label">Content:</label>
            <div id="blog-content-display" class="mle-test-display-content">{!! $blog->content !!}</div>
        </div>
    </div>

    <div class="mle-test-section">
        <h2>Featured Image</h2>
        <x-mle-media-manager-single
            id="blog-main-show"
            :model-reference="$blog"
            :collections="['image' => 'blog-main']"
            :options="['use_xhr' => $useXhr, 'theme' => $theme]"
            :readonly="true"
        />
    </div>

    <div class="mle-test-section">
        <h2>Gallery</h2>
        <x-mle-media-manager-multiple
            id="blog-gallery-show"
            :model-reference="$blog"
            :collections="['image' => 'blog-gallery']"
            :options="['use_xhr' => $useXhr, 'theme' => $theme]"
            :readonly="true"
        />
    </div>

    <div class="mle-test-section">
        <h2>Carousel Preview</h2>
        <x-mle-media-carousel
            id="blog-carousel-show"
            :model-reference="$blog"
            :collections="['image' => ['blog-main', 'blog-gallery']]"
            :options="['theme' => $theme]"
        />
    </div>

    <div class="my-5">
        <a href="{{ route('blogs.edit', ['blog' => $blog, 'theme' => $theme, 'use_xhr' => $useXhr ? 1 : 0]) }}" class="mle-test-btn mle-test-btn-warning" id="btn-edit-blog">Edit</a>
    </div>
</x-blogs-layout>
