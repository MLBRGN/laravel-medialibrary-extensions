<x-blogs-layout :theme="$theme" :title="'Edit blog: ' . $blog->title">
    <h1 class="mb-5">Edit blog: {{ $blog->title }}</h1>
    <div class="mle-test-nav">
        <a href="{{ route('blogs.index', ['theme' => $theme, 'use_xhr' => $useXhr ? 1 : 0]) }}" class="mle-test-btn">Back to blogs</a>
    </div>

    <div class="mle-test-section">
        <form action="{{ route('blogs.update', $blog) }}" method="POST" enctype="multipart/form-data" id="edit-blog-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="theme" value="{{ $theme }}">
            <input type="hidden" name="use_xhr" value="{{ $useXhr ? '1' : '0' }}">
            
            @include('blogs._form')
            
            <div class="my-5">
                <button type="submit" class="mle-test-btn mle-test-btn-primary" id="btn-update-blog">Update blog</button>
            </div>
        </form>
    </div>
    
    <div id="section-outside-form" class="mle-test-section">
        <h3>Media manager multiple (not nested, collection "blog-gallery")</h3>
        <x-mle-media-manager-multiple
            id="blog-gallery-outside"
            :model-reference="$blog"
            :collections="['image' => 'blog-gallery']"
            :options="['use_xhr' => $useXhr, 'theme' => $theme]"
            title="Gallery (Outside Form)"
        />
    </div>
</x-blogs-layout>
