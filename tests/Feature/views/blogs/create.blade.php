<x-blogs-layout :theme="$theme" title="Add a new blog">
    <h1 class="mb-5">Add a new blog</h1>
    <a href="{{ route('blogs.index', ['theme' => $theme, 'use_xhr' => $useXhr ? 1 : 0]) }}" class="btn btn-secondary mb-5">Back to blogs</a>

    <div class="row">
        <div class="col-md-8">
            <form action="{{ route('blogs.store') }}" method="POST" enctype="multipart/form-data" class="row g-3" id="create-blog-form">
                @csrf
                <input type="hidden" name="theme" value="{{ $theme }}">
                <input type="hidden" name="use_xhr" value="{{ $useXhr ? '1' : '0' }}">
                
                @include('blogs._form')
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary" id="btn-save-blog">Save</button>
                </div>
            </form>
        </div>
        
        <div class="col-md-4">
            <section id="section-outside-form">
                <h3>Outside Form Component</h3>
                <p>This component is outside the main Blog form but can still be used for temporary uploads.</p>
                <x-mle-media-manager-multiple
                    id="blog-gallery-outside"
                    :model-reference="\Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog::class"
                    :collections="['image' => 'blog-gallery']"
                    :options="['use_xhr' => $useXhr, 'temporaryUploadMode' => true, 'theme' => $theme]"
                    title="Gallery (Outside Form)"
                />
            </section>
        </div>
    </div>
</x-blogs-layout>
