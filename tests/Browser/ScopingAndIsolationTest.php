<?php

use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

/** @noinspection InvalidDatasetNameCaseInspection */

beforeEach(function () {
    config(['media-library.disk_name' => 'mle_demo_disk']);
});

dataset('isolation_matrix', [
    'bootstrap + xhr' => ['bootstrap-5', true],
    'plain + xhr' => ['plain', true],
]);

it('isolates temporary uploads between browser tabs', function (string $theme, bool $useXhr) {
    $xhrInt = $useXhr ? 1 : 0;
    $waitTime = .5;

    // 1. Open Tab A (Create Blog)
    $pageA = $this->visit("/blogs/create?theme={$theme}&use_xhr={$xhrInt}")
        ->assertSee('Add a new blog');

    // 2. Open Tab B (Create Blog)
    $pageB = $this->visit("/blogs/create?theme={$theme}&use_xhr={$xhrInt}")
        ->assertSee('Add a new blog');

    // 3. Upload "Image A" in Tab A
    $featuredInputSelector = '#blog-main-inside-mms [data-mle-media-input]';
    $featuredUploadButtonSelector = '#blog-main-inside-mms [data-mle-media-upload-button]';
    $featuredGridSelector = '#blog-main-inside-mms [data-mle-media-preview-grid]';

    $pageA->attach($featuredInputSelector, $this->getRandomFixture())
        ->pressAndWaitFor($featuredUploadButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // 4. Verify "Image A" appears in Tab A but NOT in Tab B
    $pageA->assertPresent($featuredGridSelector . ' [data-mle-media-preview-item]');
    $pageB->assertMissing($featuredGridSelector . ' [data-mle-media-preview-item]');

    // 5. Save Tab A
    $titleA = 'Blog Tab A ' . uniqid();
    $pageA->type('#title', $titleA)
        ->type('#content', 'Content A')
        ->press('#btn-save-blog')
        ->assertSee('Blog created.');

    // 6. Verify Tab B still has its own state (empty)
    $pageB->assertSee('Add a new blog')
        ->assertMissing($featuredGridSelector . ' [data-mle-media-preview-item]');

    // 7. Verify Tab A has media permanently attached
    $blogA = Blog::where('title', $titleA)->first();
    $this->assertNotNull($blogA);
    $this->assertCount(1, $blogA->getMedia('blog-main'));

    $pageA->page()->close();
    $pageB->page()->close();
})->with('isolation_matrix')->group('browser')->flaky();

it('prevents temporary uploads from leaking into carousels on show pages', function (string $theme, bool $useXhr) {
    $xhrInt = $useXhr ? 1 : 0;
    $waitTime = .5;

    // 1. Create an existing blog for the show page
    $blog = Blog::create(['title' => 'Existing Blog', 'content' => 'Content']);

    // 2. Open Tab A (Create Blog) and upload an image (temporary)
    $pageA = $this->visit("/blogs/create?theme={$theme}&use_xhr={$xhrInt}")
        ->assertSee('Add a new blog');

    $featuredInputSelector = '#blog-main-inside-mms [data-mle-media-input]';
    $featuredUploadButtonSelector = '#blog-main-inside-mms [data-mle-media-upload-button]';

    $pageA->attach($featuredInputSelector, $this->getRandomFixture())
        ->pressAndWaitFor($featuredUploadButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // 3. Open Tab B (Show Page for existing blog)
    $pageB = $this->visit("/blogs/{$blog->id}?theme={$theme}&use_xhr={$xhrInt}")
        ->assertSee('Existing Blog');

    // 4. Assert that the Carousel on the "Show" page does NOT show the unsaved image from the "Create" page
    // (Carousel uses includeTemporaryUploads = false by default now)
    $carouselSelector = '.mle-media-carousel';
    $pageB->assertPresent($carouselSelector)
        ->assertSee(__('medialibrary-extensions::messages.no_media'));

    $pageA->page()->close();
    $pageB->page()->close();
})->with('isolation_matrix')->group('browser')->flaky();
