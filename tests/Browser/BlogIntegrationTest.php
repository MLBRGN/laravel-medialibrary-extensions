<?php

use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

/** @noinspection InvalidDatasetNameCaseInspection */

beforeEach(function () {
    config(['media-library.disk_name' => 'mle_demo_disk']);
});

it('can create a blog with featured image inside and gallery outside form', function (string $theme, bool $useXhr) {
    $title = 'CRUD Create Test ' . uniqid();
    $xhrInt = $useXhr ? 1 : 0;
    $wait = $useXhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    $page = $this->visit("/blogs/create?theme={$theme}&use_xhr={$xhrInt}");

    $page->assertNoJavaScriptErrors();
    $page->assertSee('Add a new blog');

    // 1. Fill basic blog info
    $page->type('#title', $title);
    $page->type('#content', 'Blog content here');

    // 2. Upload featured image INSIDE the blog form (temporary mode)
    $featuredContainer = '[data-base-id="blog-main-inside"]';
    $featuredInputSelector = "{$featuredContainer} [data-mle-media-input]";
    $featuredUploadButtonSelector = "{$featuredContainer} [data-mle-media-upload-button]";

    $this->scrollIntoView($page, $featuredContainer);

    $page->assertPresent($featuredInputSelector);
    $page->attach($featuredInputSelector, $this->getRandomFixture());

    $page->assertPresent($featuredUploadButtonSelector);
    $page->pressAndWaitFor($featuredUploadButtonSelector, $wait);
    $page->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // 3. Upload gallery image OUTSIDE the blog form (temporary mode)
    $galleryContainer = '[data-base-id="blog-gallery-outside"]';
    $galleryInputSelector = "{$galleryContainer} [data-mle-media-input]";
    $galleryUploadButtonSelector = "{$galleryContainer} [data-mle-media-upload-button]";

    $this->scrollIntoView($page, $galleryContainer);

    $page->assertPresent($galleryInputSelector);
    $page->attach($galleryInputSelector, $this->getRandomFixture());

    $page->assertPresent($galleryUploadButtonSelector);
    $page->pressAndWaitFor($galleryUploadButtonSelector, $wait);
    $page->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // 4. Submit the main Blog form
    $page->press('#btn-save-blog');

    $page->assertSee('Blog created.');
    $page->assertSee($title);

    // 5. Verify promotion in database
    $blog = Blog::where('title', $title)->first();
    $this->assertNotNull($blog);
    $this->assertCount(1, $blog->getMedia('blog-main'));
    $this->assertCount(1, $blog->getMedia('blog-gallery'));

    // 6. Verify no orphans in DB
    $this->assertDatabaseCount('mle_temporary_uploads', 0);

    // 7. Verify visual presence on Show page (already redirected)
    $page->assertSee($title);

    $page->assertPresent('[data-base-id="blog-main-show"] [data-mle-media-preview-item]');
    $page->assertMissing('[data-base-id="blog-main-show"] [data-mle-media-preview-container]:nth-child(2)');

    $page->assertPresent('[data-base-id="blog-gallery-show"] [data-mle-media-preview-item]');
    $page->assertMissing('[data-base-id="blog-gallery-show"] [data-mle-media-preview-container]:nth-child(2)');

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();

it('can edit a blog and manage media', function (string $theme, bool $useXhr) {
    $blog = Blog::create(['title' => 'Initial CRUD Blog', 'content' => 'Content']);
    $newTitle = 'Updated CRUD Blog ' . uniqid();
    $xhrInt = $useXhr ? 1 : 0;
    $wait = $useXhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    $page = $this->visit("/blogs/{$blog->id}/edit?theme={$theme}&use_xhr={$xhrInt}");

    $page->assertNoJavaScriptErrors();
    $page->assertSee('Edit blog');

    // 1. Upload featured image (persistent mode since model exists)
    $featuredContainer = '[data-base-id="blog-main-inside"]';
    $featuredInputSelector = "{$featuredContainer} [data-mle-media-input]";
    $featuredUploadButtonSelector = "{$featuredContainer} [data-mle-media-upload-button]";

    $page->type('#title', $newTitle);

    $page->assertPresent($featuredInputSelector);
    $page->attach($featuredInputSelector, $this->getRandomFixture());

    $page->assertPresent($featuredUploadButtonSelector);
    $page->pressAndWaitFor($featuredUploadButtonSelector, $wait);
    $page->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // 2. Submit the form
    $page->press('#btn-update-blog');

    $page->assertSee('Blog updated.');
    $page->assertSee($newTitle);

    $blog->refresh();
    $this->assertEquals($newTitle, $blog->title);
    $this->assertCount(1, $blog->getMedia('blog-main'));

    // 3. Verify visual presence on Show page (already redirected)
    $page->assertPresent('[data-base-id="blog-main-show"] [data-mle-media-preview-item]');
    $page->assertMissing('[data-base-id="blog-main-show"] [data-mle-media-preview-container]:nth-child(2)');

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();

it('does not leak media between different blogs on their show pages', function (string $theme, bool $useXhr) {
    // 1. Create Blog A with 1 image
    $blogA = Blog::create(['title' => 'Blog A', 'content' => 'Content A']);
    $blogA->addMedia($this->getRandomFixture())->preservingOriginal()->toMediaCollection('blog-main');

    // 2. Create Blog B with 1 image
    $blogB = Blog::create(['title' => 'Blog B', 'content' => 'Content B']);
    $blogB->addMedia($this->getRandomFixture())->preservingOriginal()->toMediaCollection('blog-main');

    $xhrInt = $useXhr ? 1 : 0;

    // 3. Visit Show Page for Blog A
    $page = $this->visit("/blogs/{$blogA->id}?theme={$theme}&use_xhr={$xhrInt}");

    $page->assertSee('Blog A');

    // 4. Verify ONLY Blog A's media is visible
    $page->assertPresent('[data-base-id="blog-main-show"] [data-mle-media-preview-item]');
    $page->assertMissing('[data-base-id="blog-main-show"] [data-mle-media-preview-container]:nth-child(2)');

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();

it('can view blog with readonly media managers', function (string $theme, bool $useXhr) {
    $blog = Blog::create(['title' => 'View Test Blog', 'content' => 'Content']);

    // Use stable fixtures for pre-uploading
    $f1 = __DIR__ . '/../Fixtures/test2.jpg';
    $f2 = __DIR__ . '/../Fixtures/test3.jpg';

    $blog->addMedia($f1)->preservingOriginal()->toMediaCollection('blog-main');
    $blog->addMedia($f2)->preservingOriginal()->toMediaCollection('blog-gallery');

    $xhrInt = $useXhr ? 1 : 0;

    $page = $this->visit("/blogs/{$blog->id}?theme={$theme}&use_xhr={$xhrInt}");

    $page->assertNoJavaScriptErrors();
    $page->assertSee('View Test Blog');
    $page->assertSee('Featured Image');
    $page->assertSee('Gallery');

    // Verify media components are present but readonly (no upload button)
    $page->assertPresent('[data-base-id="blog-main-show"]');
    $page->assertMissing('[data-base-id="blog-main-show"] [data-mle-media-upload-button]');

    $page->assertPresent('[data-base-id="blog-gallery-show"]');
    $page->assertMissing('[data-base-id="blog-gallery-show"] [data-mle-media-upload-button]');

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();
