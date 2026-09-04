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

        <section id="section-blog-crud">
            <h2>Blog CRUD Simulation</h2>

            <div class="row">
                <div class="col-md-6 border p-3">
                    <h3>Create Blog (Inside Form)</h3>
                    <form action="{{ route('blog-showcase-update') }}" method="POST" id="create-blog-form">
                        @csrf
                        <input type="hidden" name="theme" value="{{ $theme }}">
                        <input type="hidden" name="use_xhr" value="{{ $useXhr ? '1' : '0' }}">
                        <div class="mb-3">
                            <label for="blog-title-create" class="form-label">Title</label>
                            <input type="text" name="title" id="blog-title-create" class="form-control" value="New Blog Post">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Featured Image (Temporary)</label>
                            <x-mle-media-manager-single
                                id="blog-create-featured"
                                model-reference="Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog"
                                :collections="['image' => 'blog-main']"
                                :options="['use_xhr' => $useXhr, 'temporaryUploadMode' => true]"
                            />
                        </div>
                        <input type="hidden" name="client_token" value="" data-mle-client-token>
                        <button type="submit" class="btn btn-primary" id="btn-create-blog">Save Blog (Create)</button>
                    </form>
                </div>

                <div class="col-md-6 border p-3">
                    <h3>Edit Blog (Inside Form)</h3>
                    <form action="{{ route('blog-showcase-update') }}" method="POST" id="edit-blog-form">
                        @csrf
                        <input type="hidden" name="id" value="{{ $blog->id }}">
                        <input type="hidden" name="theme" value="{{ $theme }}">
                        <input type="hidden" name="use_xhr" value="{{ $useXhr ? '1' : '0' }}">
                        <div class="mb-3">
                            <label for="blog-title-edit" class="form-label">Title</label>
                            <input type="text" name="title" id="blog-title-edit" class="form-control" value="{{ $blog->title }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gallery (Temporary)</label>
                            <x-mle-media-manager-multiple
                                id="blog-edit-gallery"
                                :model-reference="$blog"
                                :collections="['image' => 'blog-gallery']"
                                :options="['use_xhr' => $useXhr, 'temporaryUploadMode' => true]"
                            />
                        </div>
                        <input type="hidden" name="client_token" value="" data-mle-client-token>
                        <button type="submit" class="btn btn-secondary" id="btn-edit-blog">Save Blog (Update)</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
    </body>
</html>
