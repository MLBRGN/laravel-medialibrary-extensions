<?php

use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

/** @noinspection InvalidDatasetNameCaseInspection */

beforeEach(function () {
    config(['media-library.disk_name' => 'mle_demo_disk']);
});

it('handles abandoned temporary uploads in the same tab session', function (string $theme, bool $useXhr) {
    $title = 'Abandoned Test ' . uniqid();
    $xhrInt = $useXhr ? 1 : 0;

    // 1. Visit index and click create
    $page = $this->visit("/blogs/create?theme={$theme}&use_xhr={$xhrInt}");
    $url = $page->page()->url();

    // Upload an image to the gallery
    $galleryName1 = $this->uploadToGallery($page, $this->getRandomFixture());

    // Verify 1 temporary upload exists in DB
    $this->assertDatabaseCount('mle_temporary_uploads', 1);

    // 2. STOP! Refresh the page (simulating starting over in the same tab)
    $page->navigate($url)
        ->assertSee('Add a new blog');

    // The previous upload should still be visible because it's the same tab session
    $page->assertPresent('[data-base-id="blog-gallery-outside"] [data-mle-media-preview-item]');

    // 3. Upload a new image to the SAME gallery and submit
    $this->fillBlogForm($page, $title, 'Blog content');

    $galleryName2 = $this->uploadToGallery($page, $this->getRandomFixture());

    // Verify we now have 2 temporary uploads (abandoned one + new one)
    $this->assertDatabaseCount('mle_temporary_uploads', 2);

    // 4. Submit the form
    $page->click('#btn-save-blog')
        ->assertSee('Blog created.');

    $blog = Blog::where('title', $title)->first();
    $this->assertNotNull($blog);

    // Should have 2 media because we uploaded twice to the gallery
    $this->assertCount(2, $blog->getMedia('blog-gallery'));

    $this->assertGalleryImagesVisible($page, [$galleryName1, $galleryName2]);

    // CRITICAL: Verify NO orphans remain in mle_temporary_uploads
    $this->assertDatabaseCount('mle_temporary_uploads', 0);

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser');

it('does not leak media between different blogs on their show pages', function (string $theme, bool $useXhr) {
    // 1. Create Blog A with 1 image
    $blogA = Blog::create(['title' => 'Blog A', 'content' => 'Content A']);
    $fixtureA = $this->getRandomFixture();
    $fixtureAName = basename($fixtureA);
    $blogA->addMedia($fixtureA)->preservingOriginal()->toMediaCollection('blog-main');

    // 2. Create Blog B with 1 image
    $blogB = Blog::create(['title' => 'Blog B', 'content' => 'Content B']);
    $fixtureB = $this->getFixtureAsFilePath('test2.jpg');
    if (basename($fixtureB) === $fixtureAName) {
        $fixtureB = $this->getFixtureAsFilePath('test3.jpg');
    }
    $fixtureBName = basename($fixtureB);
    $blogB->addMedia($fixtureB)->preservingOriginal()->toMediaCollection('blog-main');

    $xhrInt = $useXhr ? 1 : 0;

    // 3. Visit Show Page for Blog A via index
    $page = $this->visit("/blogs?theme={$theme}&use_xhr={$xhrInt}");
    $page->click("#btn-show-{$blogA->id}")
        ->assertSee('Blog A');

    // 4. Verify ONLY Blog A's media is visible
    $container = '[data-base-id="blog-main-show"]';
    $page->assertPresent("{$container} [data-mle-media-preview-item]")
        ->assertMissing("{$container} [data-mle-media-preview-container]:nth-child(2)");
    
    $src = $page->page()->locator("{$container} [data-mle-media-preview-image]")->first()->getAttribute('src');
    expect($src)->toContain($fixtureAName)
        ->not->toContain($fixtureBName);

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser');
