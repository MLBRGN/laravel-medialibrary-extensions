<?php

use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

/** @noinspection InvalidDatasetNameCaseInspection */

beforeEach(function () {
    config(['media-library.disk_name' => 'mle_demo_disk']);
});

dataset('blog_crud_matrix', [
    'bootstrap + xhr' => ['bootstrap-5', true],
    'plain + xhr' => ['plain', true],
]);

it('can create a blog with featured image inside and gallery outside form', function (string $theme, bool $useXhr) {
    $title = 'CRUD Create Test ' . uniqid();
    $xhrInt = $useXhr ? 1 : 0;
    $waitTime = .5;

    $page = $this->visit("/blogs/create?theme={$theme}&use_xhr={$xhrInt}")
        ->assertNoJavaScriptErrors()
        ->assertSee('Add a new blog');

    // 1. Upload featured image INSIDE the blog form (temporary mode)
    $featuredInputSelector = '#blog-main-inside-mms [data-mle-media-input]';
    $featuredUploadButtonSelector = '#blog-main-inside-mms [data-mle-media-upload-button]';

    $page->type('#title', $title)
        ->type('#content', 'Blog content here');

    $this->scrollIntoView($page, '#blog-main-inside-mms');
    $page->attach($featuredInputSelector, $this->getRandomFixture())
        ->assertPresent($featuredUploadButtonSelector)
        ->pressAndWaitFor($featuredUploadButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // 2. Upload gallery image OUTSIDE the blog form (temporary mode)
    $galleryInputSelector = '#blog-gallery-outside-mmm [data-mle-media-input]';
    $galleryUploadButtonSelector = '#blog-gallery-outside-mmm [data-mle-media-upload-button]';

    $this->scrollIntoView($page, '#blog-gallery-outside-mmm');
    $page->attach($galleryInputSelector, $this->getRandomFixture())
        ->assertPresent($galleryUploadButtonSelector)
        ->pressAndWaitFor($galleryUploadButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // 3. Submit the main Blog form
    $page->press('#btn-save-blog')
        ->assertSee('Blog created.')
        ->assertSee($title);

    // 4. Verify promotion
    $blog = Blog::where('title', $title)->first();
    $this->assertNotNull($blog);
    $this->assertCount(1, $blog->getMedia('blog-main'));
    $this->assertCount(1, $blog->getMedia('blog-gallery'));

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();

it('can edit a blog and manage media', function (string $theme, bool $useXhr) {
    $blog = Blog::create(['title' => 'Initial CRUD Blog', 'content' => 'Content']);
    $newTitle = 'Updated CRUD Blog ' . uniqid();
    $xhrInt = $useXhr ? 1 : 0;
    $waitTime = .5;

    $page = $this->visit("/blogs/{$blog->id}/edit?theme={$theme}&use_xhr={$xhrInt}")
        ->assertNoJavaScriptErrors()
        ->assertSee('Edit blog');

    // 1. Upload featured image (persistent mode since model exists)
    $featuredInputSelector = '#blog-main-inside-mms [data-mle-media-input]';
    $featuredUploadButtonSelector = '#blog-main-inside-mms [data-mle-media-upload-button]';

    $page->type('#title', $newTitle)
        ->attach($featuredInputSelector, $this->getRandomFixture())
        ->assertPresent($featuredUploadButtonSelector)
        ->pressAndWaitFor($featuredUploadButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // 2. Submit the form
    $page->press('#btn-update-blog')
        ->assertSee('Blog updated.')
        ->assertSee($newTitle);

    $blog->refresh();
    $this->assertEquals($newTitle, $blog->title);
    $this->assertCount(1, $blog->getMedia('blog-main'));

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

    $page = $this->visit("/blogs/{$blog->id}?theme={$theme}&use_xhr={$xhrInt}")
        ->assertNoJavaScriptErrors()
        ->assertSee('View Test Blog')
        ->assertSee('Featured Image')
        ->assertSee('Gallery');

    // Verify media components are present but readonly (no upload button)
    $page->assertPresent('#blog-main-show-mms')
        ->assertMissing('#blog-main-show-mms [data-mle-media-upload-button]')
        ->assertPresent('#blog-gallery-show-mmm')
        ->assertMissing('#blog-gallery-show-mmm [data-mle-media-upload-button]');

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();
