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

    $page = $this->visit("/blogs?theme={$theme}&use_xhr={$xhrInt}");
    $this->waitForMLE($page);
    $page->click('#btn-create-new')
        ->assertSee('Add a new blog');

    // 1. Fill basic blog info
    $this->fillBlogForm($page, $title, 'Blog content here');

    // 2. Upload featured image INSIDE the blog form (temporary mode)
    $featuredName = $this->uploadFeaturedImage($page, $this->getRandomFixture());

    // 3. Upload gallery image OUTSIDE the blog form (temporary mode)
    $galleryName = $this->uploadToGallery($page, $this->getRandomFixture());

    // 4. Submit the main Blog form
    $this->waitForMLE($page);
    $page->click('#btn-save-blog')
        ->assertSee('Blog created.')
        ->assertSee($title);

    // 5. Verify promotion in database
    $blog = Blog::where('title', $title)->first();
    $this->assertNotNull($blog);
    $this->assertCount(1, $blog->getMedia('blog-main'));
    $this->assertCount(1, $blog->getMedia('blog-gallery'));

    // 6. Verify no orphans in DB
    $this->assertDatabaseCount('mle_temporary_uploads', 0);

    // 7. Verify visual presence on Show page
    $this->assertFeaturedImageVisible($page, $featuredName);
    $this->assertGalleryImagesVisible($page, [$galleryName]);

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();

it('can edit a blog and manage media', function (string $theme, bool $useXhr) {
    $blog = Blog::create(['title' => 'Initial CRUD Blog', 'content' => 'Content']);
    $newTitle = 'Updated CRUD Blog ' . uniqid();
    $xhrInt = $useXhr ? 1 : 0;
    $wait = $useXhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    $page = $this->visit("/blogs?theme={$theme}&use_xhr={$xhrInt}");
    $this->waitForMLE($page);
    $page->click("#btn-edit-{$blog->id}")
        ->assertSee('Edit blog');

    $page->type('#title', $newTitle);

    // 1. Upload featured image (persistent mode since model exists)
    $featuredName = $this->uploadFeaturedImage($page, $this->getRandomFixture());

    // 2. Submit the form
    $this->waitForMLE($page);
    $page->click('#btn-update-blog')
        ->assertSee('Blog updated.')
        ->assertSee($newTitle);

    $blog->refresh();
    $this->assertEquals($newTitle, $blog->title);
    $this->assertCount(1, $blog->getMedia('blog-main'));

    // 3. Verify visual presence on Show page
    $this->assertFeaturedImageVisible($page, $featuredName);

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();

it('can view blog with readonly media managers', function (string $theme, bool $useXhr) {
    $blog = Blog::create(['title' => 'View Test Blog', 'content' => 'Content']);

    $f1 = $this->getFixtureAsFilePath('test2.jpg');
    $f2 = $this->getFixtureAsFilePath('test3.jpg');

    $blog->addMedia($f1)->preservingOriginal()->toMediaCollection('blog-main');
    $blog->addMedia($f2)->preservingOriginal()->toMediaCollection('blog-gallery');

    $xhrInt = $useXhr ? 1 : 0;

    $page = $this->visit("/blogs?theme={$theme}&use_xhr={$xhrInt}");
    $this->waitForMLE($page);
    $page->click("#btn-show-{$blog->id}")
        ->assertSee('View Test Blog')
        ->assertSee('Featured Image')
        ->assertSee('Gallery');

    // Verify media components are present but readonly (no upload button)
    $page->assertPresent('[data-base-id="blog-main-show"]')
        ->assertMissing('[data-base-id="blog-main-show"] [data-mle-media-upload-button]');

    $this->assertFeaturedImageVisible($page, 'test2.jpg');

    $page->assertPresent('[data-base-id="blog-gallery-show"]')
        ->assertMissing('[data-base-id="blog-gallery-show"] [data-mle-media-upload-button]');

    $this->assertGalleryImagesVisible($page, ['test3.jpg']);

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();
