@php
    /** @var \Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog $blog */
    /** @var string $mode */
    /** @var bool $useXhr */
    /** @var string $theme */
@endphp

<div class="mle-test-form-group">
    <label for="title" class="mle-test-label">Title</label>
    <input type="text" name="title" id="title" class="mle-test-input" value="{{ old('title', $blog->title) }}" required>
</div>

<div class="mle-test-form-group">
    <label for="content" class="mle-test-label">Content</label>
    @if (class_exists(\Mlbrgn\LaravelFormComponents\Providers\FormComponentsServiceProvider::class))
        <x-form-html-editor
            name="content"
            id="content"
            label="Content"
            data-base-id="blog-content-editor"
            :tinymce-config="[]"
            :extra-form-data="[
                'model_type' => $blog->getMorphClass(),
                'model_id' => $blog->getKey(),
                'collection_name' => 'blog-content',
                'collections' => ['image' => 'blog-content'],
                'data_source' => 'default',
            ]"
            data-mle-model-type="{{ $blog->getMorphClass() }}"
            data-mle-model-id="{{ $blog->getKey() }}"
            data-mle-data-source="default"
            :data-mle-collections="json_encode([
                'image' => 'blog-content',
            ])"
        />
    @else
        <textarea name="content" id="content" class="mle-test-textarea" rows="5">{{ old('content', $blog->content) }}</textarea>
    @endif
</div>

<div class="mle-test-form-group">
    <h2>Media manager single (Nested, collection "blog-main")</h2>
    @if($mode === 'edit')
        <x-mle-media-manager-single
            id="blog-main-inside"
            :model-reference="$blog"
            :collections="['image' => 'blog-main']"
            :options="['use_xhr' => $useXhr, 'theme' => $theme]"
            title="Featured Image"
        />
    @else
        <x-mle-media-manager-single
            id="blog-main-inside"
            :model-reference="$blog->getMorphClass()"
            :collections="['image' => 'blog-main']"
            :options="['use_xhr' => $useXhr, 'temporaryUploadMode' => true, 'theme' => $theme]"
            title="Featured Image"
        />
    @endif
</div>

