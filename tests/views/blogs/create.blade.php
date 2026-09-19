<x-blogs-layout :theme="$theme" title="Add a new blog">
    <h1 class="mb-5">Add a new blog</h1>
    <div class="mle-test-nav">
        <a href="{{ route('blogs.index', ['theme' => $theme, 'use_xhr' => $useXhr ? 1 : 0]) }}" class="mle-test-btn">Back to blogs</a>
    </div>

    <div class="mle-test-section">
        <form action="{{ route('blogs.store') }}" method="POST" enctype="multipart/form-data" id="create-blog-form">
            @csrf
            <input type="hidden" name="theme" value="{{ $theme }}">
            <input type="hidden" name="use_xhr" value="{{ $useXhr ? '1' : '0' }}">
            
            @include('blogs._form')

            <div id="section-outside-form" class="mle-test-section">
                <h3>Media manager multiple (collection "blog-gallery")</h3>
                <x-mle-media-manager-multiple
                    id="blog-gallery-outside"
                    name="gallery"
                    :model-reference="\Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog::class"
                    :collections="['image' => 'blog-gallery']"
                    :options="['use_xhr' => $useXhr, 'temporaryUploadMode' => true, 'theme' => $theme, 'minMediaCount' => 2]"
                    title="Gallery (Outside Form)"
                />
                @error('gallery')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="my-5">
                <button type="submit" class="mle-test-btn mle-test-btn-primary" id="btn-save-blog">Save blog</button>
            </div>
        </form>
    </div>
</x-blogs-layout>
