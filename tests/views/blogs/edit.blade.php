<x-blogs-layout :theme="$theme" :title="'Edit blog: ' . $blog->title">
    <h1 class="mb-5">Edit blog: {{ $blog->title }}</h1>
    <a href="{{ route('blogs.index', ['theme' => $theme, 'use_xhr' => $useXhr ? 1 : 0]) }}" class="btn btn-secondary mb-5">Back to blogs</a>

    <div class="row">
        <div class="col-md-8">
            <form action="{{ route('blogs.update', $blog) }}" method="POST" enctype="multipart/form-data" class="row g-3" id="edit-blog-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="theme" value="{{ $theme }}">
                <input type="hidden" name="use_xhr" value="{{ $useXhr ? '1' : '0' }}">
                
                @include('blogs._form')
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary" id="btn-update-blog">Update</button>
                </div>
            </form>
        </div>
        
        <div class="col-md-4">
            <section id="section-outside-form">
                <h3>Outside Form Component</h3>
                <p>Managing gallery for existing blog, outside the main form.</p>
                <x-mle-media-manager-multiple
                    id="blog-gallery-outside"
                    :model-reference="$blog"
                    :collections="['image' => 'blog-gallery']"
                    :options="['use_xhr' => $useXhr, 'theme' => $theme]"
                    title="Gallery (Outside Form)"
                />
            </section>
        </div>
    </div>
</x-blogs-layout>
