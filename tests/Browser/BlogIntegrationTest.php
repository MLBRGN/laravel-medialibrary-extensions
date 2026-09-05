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
    $page->click('#btn-create-new');

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
    $featuredFixture = $this->getRandomFixture();
    $featuredName = str_replace('_', '-', basename($featuredFixture));
    $page->attach($featuredInputSelector, $featuredFixture);

    $page->assertPresent($featuredUploadButtonSelector);
    $page->pressAndWaitFor($featuredUploadButtonSelector, $wait);
    $page->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // 3. Upload gallery image OUTSIDE the blog form (temporary mode)
    $galleryContainer = '[data-base-id="blog-gallery-outside"]';
    $galleryInputSelector = "{$galleryContainer} [data-mle-media-input]";
    $galleryUploadButtonSelector = "{$galleryContainer} [data-mle-media-upload-button]";

    $this->scrollIntoView($page, $galleryContainer);

    $page->assertPresent($galleryInputSelector);
    $galleryFixture = $this->getRandomFixture();
    $galleryName = str_replace('_', '-', basename($galleryFixture));
    $page->attach($galleryInputSelector, $galleryFixture);

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
    expect($page->page()->locator('[data-base-id="blog-main-show"] [data-mle-media-preview-item] [data-mle-media-preview-image]')->first()->getAttribute('src'))
        ->toContain($featuredName);

    $page->assertPresent('[data-base-id="blog-gallery-show"] [data-mle-media-preview-item]');
    $page->assertMissing('[data-base-id="blog-gallery-show"] [data-mle-media-preview-container]:nth-child(2)');
    expect($page->page()->locator('[data-base-id="blog-gallery-show"] [data-mle-media-preview-item] [data-mle-media-preview-image]')->first()->getAttribute('src'))
        ->toContain($galleryName);

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();

it('simulates a human workflow: index -> create -> upload -> show', function (string $theme, bool $useXhr) {
    $title = 'Human Flow Test ' . uniqid();
    $xhrInt = $useXhr ? 1 : 0;
    $wait = $useXhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    // 1. Start at the index page
    $page = $this->visit("/blogs?theme={$theme}&use_xhr={$xhrInt}");
    $page->assertSee('All Blogs');

    // 2. Click "Create New Blog"
    $page->click('#btn-create-new');
    $page->assertSee('Add a new blog');

    // 3. Fill out the form
    $page->type('#title', $title);
    $page->type('#content', 'Blog content by human');

    // 4. Add medium to media manager (Featured Image - Single)
    $featuredContainer = '[data-base-id="blog-main-inside"]';
    $featuredInputSelector = "{$featuredContainer} [data-mle-media-input]";
    $featuredUploadButtonSelector = "{$featuredContainer} [data-mle-media-upload-button]";

    $this->scrollIntoView($page, $featuredContainer);
    $featuredFixture = $this->getRandomFixture();
    $featuredName = str_replace('_', '-', basename($featuredFixture));
    $page->attach($featuredInputSelector, $featuredFixture);
    $page->pressAndWaitFor($featuredUploadButtonSelector, $wait);
    $page->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // 5. Add 2 media to Gallery (Multiple)
    $galleryContainer = '[data-base-id="blog-gallery-outside"]';
    $galleryInputSelector = "{$galleryContainer} [data-mle-media-input]";
    $galleryUploadButtonSelector = "{$galleryContainer} [data-mle-media-upload-button]";

    $this->scrollIntoView($page, $galleryContainer);
    
    // First upload to gallery
    $galleryFixture1 = $this->getRandomFixture();
    $galleryName1 = str_replace('_', '-', basename($galleryFixture1));
    $page->attach($galleryInputSelector, $galleryFixture1);
    $page->pressAndWaitFor($galleryUploadButtonSelector, $wait);
    $page->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // Second upload to gallery
    $galleryFixture2 = $this->getRandomFixture();
    $galleryName2 = str_replace('_', '-', basename($galleryFixture2));
    $page->attach($galleryInputSelector, $galleryFixture2);
    $page->pressAndWaitFor($galleryUploadButtonSelector, $wait);
    $page->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // 6. Submit the form
    $page->press('#btn-save-blog');

    // 7. Redirected to show page - verify success message and content
    $page->assertSee('Blog created.');
    $page->assertSee($title);

    // 8. Check Featured Image (Single)
    $page->assertPresent('[data-base-id="blog-main-show"] [data-mle-media-preview-item]');
    $page->assertMissing('[data-base-id="blog-main-show"] [data-mle-media-preview-container]:nth-child(2)');
    expect($page->page()->locator('[data-base-id="blog-main-show"] [data-mle-media-preview-item] [data-mle-media-preview-image]')->first()->getAttribute('src'))
        ->toContain($featuredName);

    // 9. Check Gallery (Multiple - should have 2)
    $page->assertPresent('[data-base-id="blog-gallery-show"] [data-mle-media-preview-container]:nth-child(1) [data-mle-media-preview-item]');
    $page->assertPresent('[data-base-id="blog-gallery-show"] [data-mle-media-preview-container]:nth-child(2) [data-mle-media-preview-item]');
    $page->assertMissing('[data-base-id="blog-gallery-show"] [data-mle-media-preview-container]:nth-child(3)');

    $gallerySrc1 = $page->page()->locator('[data-base-id="blog-gallery-show"] [data-mle-media-preview-container]:nth-child(1) [data-mle-media-preview-item] [data-mle-media-preview-image]')->first()->getAttribute('src');
    $gallerySrc2 = $page->page()->locator('[data-base-id="blog-gallery-show"] [data-mle-media-preview-container]:nth-child(2) [data-mle-media-preview-item] [data-mle-media-preview-image]')->first()->getAttribute('src');
    
    // We don't necessarily know the order Spatie will return them in if they were uploaded rapidly, but usually they are sequential
    $galleryNames = [$galleryName1, $galleryName2];
    expect(str_contains($gallerySrc1, $galleryName1) || str_contains($gallerySrc1, $galleryName2))->toBeTrue();
    expect(str_contains($gallerySrc2, $galleryName1) || str_contains($gallerySrc2, $galleryName2))->toBeTrue();

    // 10. Check Carousel (should have 3 items total)
    $page->assertPresent('[data-base-id="blog-carousel-show"]');
    $page->assertPresent('[data-base-id="blog-carousel-show"] [data-mle-carousel-item]:nth-child(1)');
    $page->assertPresent('[data-base-id="blog-carousel-show"] [data-mle-carousel-item]:nth-child(2)');
    $page->assertPresent('[data-base-id="blog-carousel-show"] [data-mle-carousel-item]:nth-child(3)');
    $page->assertMissing('[data-base-id="blog-carousel-show"] [data-mle-carousel-item]:nth-child(4)');

    $carouselSrcs = [
        $page->page()->locator('[data-base-id="blog-carousel-show"] [data-mle-carousel-item]:nth-child(1) [data-mle-media-preview-image]')->first()->getAttribute('src'),
        $page->page()->locator('[data-base-id="blog-carousel-show"] [data-mle-carousel-item]:nth-child(2) [data-mle-media-preview-image]')->first()->getAttribute('src'),
        $page->page()->locator('[data-base-id="blog-carousel-show"] [data-mle-carousel-item]:nth-child(3) [data-mle-media-preview-image]')->first()->getAttribute('src'),
    ];
    
    $allNames = [$featuredName, $galleryName1, $galleryName2];
    foreach ($allNames as $name) {
        $found = false;
        foreach ($carouselSrcs as $src) {
            if (str_contains($src, $name)) {
                $found = true;
                break;
            }
        }
        expect($found)->toBeTrue("Image $name not found in carousel");
    }

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();

it('simulates a full human CRUD lifecycle: create -> show -> modal check -> edit -> show', function (string $theme, bool $useXhr) {
    $title = 'Human CRUD Test ' . uniqid();
    $xhrInt = $useXhr ? 1 : 0;
    $wait = $useXhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    // 1. Start at the index page and go to Create
    $page = $this->visit("/blogs?theme={$theme}&use_xhr={$xhrInt}");
    $page->click('#btn-create-new');
    $page->assertSee('Add a new blog');

    // 2. Fill out the form and upload media
    $page->type('#title', $title);
    $page->type('#content', 'Full lifecycle test content');

    // Featured Image
    $featuredContainer = '[data-base-id="blog-main-inside"]';
    $featuredInputSelector = "{$featuredContainer} [data-mle-media-input]";
    $featuredUploadButtonSelector = "{$featuredContainer} [data-mle-media-upload-button]";
    $featuredFixture = $this->getRandomFixture();
    $featuredName = str_replace('_', '-', basename($featuredFixture));
    $this->scrollIntoView($page, $featuredContainer);
    $page->attach($featuredInputSelector, $featuredFixture);
    $page->pressAndWaitFor($featuredUploadButtonSelector, $wait);

    // Gallery (2 images)
    $galleryContainer = '[data-base-id="blog-gallery-outside"]';
    $galleryInputSelector = "{$galleryContainer} [data-mle-media-input]";
    $galleryUploadButtonSelector = "{$galleryContainer} [data-mle-media-upload-button]";
    $this->scrollIntoView($page, $galleryContainer);

    $galleryFixture1 = $this->getRandomFixture();
    $galleryName1 = str_replace('_', '-', basename($galleryFixture1));
    $page->attach($galleryInputSelector, $galleryFixture1);
    $page->pressAndWaitFor($galleryUploadButtonSelector, $wait);

    $galleryFixture2 = $this->getRandomFixture();
    $galleryName2 = str_replace('_', '-', basename($galleryFixture2));
    $page->attach($galleryInputSelector, $galleryFixture2);
    $page->pressAndWaitFor($galleryUploadButtonSelector, $wait);

    // 3. Submit the form
    $page->press('#btn-save-blog');
    $page->assertSee('Blog created.');

    // 4. Verify on Show page
    $page->assertSee($title);

    // Verify Featured Image
    expect($page->page()->locator('[data-base-id="blog-main-show"] [data-mle-media-preview-image]')->first()->getAttribute('src'))
        ->toContain($featuredName);

    // Verify Gallery counts
    $page->assertPresent('[data-base-id="blog-gallery-show"] [data-mle-media-preview-container]:nth-child(2)');
    $page->assertMissing('[data-base-id="blog-gallery-show"] [data-mle-media-preview-container]:nth-child(3)');

    // 5. Modal Carousel Verification
    // Click the 2nd gallery image
    $galleryItem2Selector = '[data-base-id="blog-gallery-show"] [data-mle-media-preview-container]:nth-child(2) [data-mle-media-preview-item]';
    $page->click($galleryItem2Selector);

    // Assert modal is open
    $modalSelector = $theme === 'bootstrap-5' ? '.modal.show' : '[data-mle-modal].active';
    $page->assertPresent($modalSelector);

    // In the modal carousel, check the active item matches image 2
    $page->assertPresent($modalSelector . ' [data-mle-carousel-item].active [data-mle-media-preview-image][src*="' . $galleryName2 . '"]');

    // Verify counts in modal carousel (should be 2 gallery items)
    $carouselItemsInModal = $page->page()->locator($modalSelector . ' [data-mle-carousel-item]');
    expect($carouselItemsInModal->count())->toBe(2);

    // Close modal
    if ($theme === 'bootstrap-5') {
        $page->keys($modalSelector, 'Escape');
    } else {
        $page->click($modalSelector . ' [data-mle-modal-close]');
    }
    $page->assertMissing($modalSelector);

    // 6. Edit Flow
    $page->click('a.btn-warning'); // Edit button on show page
    $page->assertSee('Edit blog');

    $newTitle = 'Human CRUD Edit ' . uniqid();
    $page->type('#title', $newTitle);

    // Delete featured image
    $this->scrollIntoView($page, $featuredContainer);
    $page->click("{$featuredContainer} [data-mle-media-delete-button]");
    $page->wait(0.5); // Brief wait for DOM/XHR
    $page->assertMissing("{$featuredContainer} [data-mle-media-preview-item]");

    // Upload new featured image
    $newFeaturedFixture = $this->getRandomFixture();
    $newFeaturedName = basename($newFeaturedFixture);
    $page->attach($featuredInputSelector, $newFeaturedFixture);
    $page->pressAndWaitFor($featuredUploadButtonSelector, $wait);
    $page->assertPresent("{$featuredContainer} [data-mle-media-preview-item]");

    // 7. Save and verify
    $page->press('#btn-update-blog');
    $page->assertSee('Blog updated.');
    $page->assertSee($newTitle);

    expect($page->page()->locator('[data-base-id="blog-main-show"] [data-mle-media-preview-image]')->first()->getAttribute('src'))
        ->toContain($newFeaturedName);
    expect($page->page()->locator('[data-base-id="blog-main-show"] [data-mle-media-preview-image]')->first()->getAttribute('src'))
        ->not->toContain($featuredName);

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();

it('handles abandoned temporary uploads in the same tab session', function (string $theme, bool $useXhr) {
    $title = 'Abandoned Test ' . uniqid();
    $xhrInt = $useXhr ? 1 : 0;
    $wait = 1.0; // Use a stable wait time

    // 1. Visit index and click create
    $page = $this->visit("/blogs?theme={$theme}&use_xhr={$xhrInt}");
    $page->click('#btn-create-new');
    $url = $page->page()->url();

    $galleryContainer = '[data-base-id="blog-gallery-outside"]';
    $galleryInputSelector = "{$galleryContainer} [data-mle-media-input]";
    $galleryUploadButtonSelector = "{$galleryContainer} [data-mle-media-upload-button]";

    $this->scrollIntoView($page, $galleryContainer);
    $galleryFixture1 = $this->getRandomFixture();
    $galleryName1 = str_replace('_', '-', basename($galleryFixture1));
    $page->attach($galleryInputSelector, $galleryFixture1);
    $page->pressAndWaitFor($galleryUploadButtonSelector, $wait);
    $page->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // Verify 1 temporary upload exists in DB
    $this->assertDatabaseCount('mle_temporary_uploads', 1);

    // 2. STOP! Refresh the page (simulating starting over in the same tab)
    $page->navigate($url);
    $page->assertSee('Add a new blog');

    // The previous upload should still be visible because it's the same tab session
    $page->assertPresent($galleryContainer . ' [data-mle-media-preview-item]');

    // 3. Upload a new image to the SAME gallery and submit
    $page->type('#title', $title);
    $page->type('#content', 'Blog content');

    $this->scrollIntoView($page, $galleryContainer);
    $galleryFixture2 = $this->getRandomFixture();
    $galleryName2 = str_replace('_', '-', basename($galleryFixture2));
    $page->attach($galleryInputSelector, $galleryFixture2);
    $page->pressAndWaitFor($galleryUploadButtonSelector, $wait);
    $page->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // Verify we now have 2 temporary uploads (abandoned one + new one)
    $this->assertDatabaseCount('mle_temporary_uploads', 2);

    // 4. Submit the form
    $page->press('#btn-save-blog');

    // 5. Verify results
    $page->assertSee('Blog created.');

    $blog = Blog::where('title', $title)->first();
    $this->assertNotNull($blog);

    // Should have 2 media because we uploaded twice to the gallery
    $this->assertCount(2, $blog->getMedia('blog-gallery'));

    $galleryShowContainer = '[data-base-id="blog-gallery-show"]';
    $gallerySrc1 = $page->page()->locator($galleryShowContainer . ' [data-mle-media-preview-container]:nth-child(1) [data-mle-media-preview-item] [data-mle-media-preview-image]')->first()->getAttribute('src');
    $gallerySrc2 = $page->page()->locator($galleryShowContainer . ' [data-mle-media-preview-container]:nth-child(2) [data-mle-media-preview-item] [data-mle-media-preview-image]')->first()->getAttribute('src');
    
    expect(str_contains($gallerySrc1, $galleryName1) || str_contains($gallerySrc1, $galleryName2))->toBeTrue();
    expect(str_contains($gallerySrc2, $galleryName1) || str_contains($gallerySrc2, $galleryName2))->toBeTrue();

    // CRITICAL: Verify NO orphans remain in mle_temporary_uploads
    $this->assertDatabaseCount('mle_temporary_uploads', 0);

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();

it('can edit a blog and manage media', function (string $theme, bool $useXhr) {
    $blog = Blog::create(['title' => 'Initial CRUD Blog', 'content' => 'Content']);
    $newTitle = 'Updated CRUD Blog ' . uniqid();
    $xhrInt = $useXhr ? 1 : 0;
    $wait = $useXhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    $page = $this->visit("/blogs?theme={$theme}&use_xhr={$xhrInt}");
    $page->click("#btn-edit-{$blog->id}");

    $page->assertNoJavaScriptErrors();
    $page->assertSee('Edit blog');

    // 1. Upload featured image (persistent mode since model exists)
    $featuredContainer = '[data-base-id="blog-main-inside"]';
    $featuredInputSelector = "{$featuredContainer} [data-mle-media-input]";
    $featuredUploadButtonSelector = "{$featuredContainer} [data-mle-media-upload-button]";

    $page->type('#title', $newTitle);

    $page->assertPresent($featuredInputSelector);
    $featuredFixture = $this->getRandomFixture();
    $featuredName = basename($featuredFixture);
    $page->attach($featuredInputSelector, $featuredFixture);

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
    expect($page->page()->locator('[data-base-id="blog-main-show"] [data-mle-media-preview-item] [data-mle-media-preview-image]')->first()->getAttribute('src'))
        ->toContain($featuredName);

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();

it('does not leak media between different blogs on their show pages', function (string $theme, bool $useXhr) {
    // 1. Create Blog A with 1 image
    $blogA = Blog::create(['title' => 'Blog A', 'content' => 'Content A']);
    $fixtureA = $this->getRandomFixture();
    $fixtureAName = basename($fixtureA);
    $blogA->addMedia($fixtureA)->preservingOriginal()->toMediaCollection('blog-main');

    // 2. Create Blog B with 1 image
    $blogB = Blog::create(['title' => 'Blog B', 'content' => 'Content B']);
    $fixtureB = $this->getRandomFixture();
    $fixtureBName = basename($fixtureB);
    $blogB->addMedia($fixtureB)->preservingOriginal()->toMediaCollection('blog-main');

    $xhrInt = $useXhr ? 1 : 0;

    // 3. Visit Show Page for Blog A via index
    $page = $this->visit("/blogs?theme={$theme}&use_xhr={$xhrInt}");
    $page->click("#btn-show-{$blogA->id}");

    $page->assertSee('Blog A');

    // 4. Verify ONLY Blog A's media is visible
    $page->assertPresent('[data-base-id="blog-main-show"] [data-mle-media-preview-item]');
    $page->assertMissing('[data-base-id="blog-main-show"] [data-mle-media-preview-container]:nth-child(2)');
    
    $src = $page->page()->locator('[data-base-id="blog-main-show"] [data-mle-media-preview-item] [data-mle-media-preview-image]')->first()->getAttribute('src');
    expect($src)->toContain($fixtureAName);
    expect($src)->not->toContain($fixtureBName);

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

    $page = $this->visit("/blogs?theme={$theme}&use_xhr={$xhrInt}");
    $page->click("#btn-show-{$blog->id}");

    $page->assertNoJavaScriptErrors();
    $page->assertSee('View Test Blog');
    $page->assertSee('Featured Image');
    $page->assertSee('Gallery');

    // Verify media components are present but readonly (no upload button)
    $page->assertPresent('[data-base-id="blog-main-show"]');
    $page->assertMissing('[data-base-id="blog-main-show"] [data-mle-media-upload-button]');
    expect($page->page()->locator('[data-base-id="blog-main-show"] [data-mle-media-preview-item] [data-mle-media-preview-image]')->first()->getAttribute('src'))
        ->toContain('test2.jpg');

    $page->assertPresent('[data-base-id="blog-gallery-show"]');
    $page->assertMissing('[data-base-id="blog-gallery-show"] [data-mle-media-upload-button]');
    expect($page->page()->locator('[data-base-id="blog-gallery-show"] [data-mle-media-preview-item] [data-mle-media-preview-image]')->first()->getAttribute('src'))
        ->toContain('test3.jpg');

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();
