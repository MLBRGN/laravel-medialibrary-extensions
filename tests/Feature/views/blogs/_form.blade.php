@php
    /** @var \Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog $blog */
    /** @var string $mode */
    /** @var bool $useXhr */
    /** @var string $theme */
@endphp

<div class="mb-3">
    <label for="title" class="form-label">Title</label>
    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $blog->title) }}" required>
</div>

<div class="mb-3">
    <label for="content" class="form-label">Content</label>
    <textarea name="content" id="content" class="form-control" rows="5">{{ old('content', $blog->content) }}</textarea>
</div>

<div class="mb-3">
    <h2>Featured Image (Inside Form)</h2>
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

{{-- Hidden input for client token to ensure promotion works --}}
<input type="hidden" name="client_token" value="{{ app(\Mlbrgn\MediaLibraryExtensions\Support\ClientContext::class)->get() }}" data-mle-client-token>
