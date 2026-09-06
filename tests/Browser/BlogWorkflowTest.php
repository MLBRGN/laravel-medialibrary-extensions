<?php

/** @noinspection InvalidDatasetNameCaseInspection */

beforeEach(function () {
    config(['media-library.disk_name' => 'mle_demo_disk']);
});

it('simulates a human workflow: index -> create -> upload -> show', function (string $theme, bool $useXhr) {
    $title = 'Human Flow Test ' . uniqid();
    $xhrInt = $useXhr ? 1 : 0;
    $wait = $useXhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    // 1. Start at the index page and go to Create
    $page = $this->visit("/blogs?theme={$theme}&use_xhr={$xhrInt}");
    $page->assertSee('All Blogs')
        ->click('#btn-create-new')
        ->assertSee('Add a new blog');

    // 2. Fill out the form
    $this->fillBlogForm($page, $title, 'Blog content by human');

    // 3. Add medium to media manager (Featured Image - Single)
    $featuredName = $this->uploadFeaturedImage($page, $this->getRandomFixture());

    // 4. Add 2 media to Gallery (Multiple)
    $galleryNames = [];
    $galleryNames[] = $this->uploadToGallery($page, $this->getRandomFixture());
    $galleryNames[] = $this->uploadToGallery($page, $this->getRandomFixture());

    // 5. Submit the form
    $page->press('#btn-save-blog')
        ->assertSee('Blog created.')
        ->assertSee($title);

    // 6. Check Visual Presence on Show page
    $this->assertFeaturedImageVisible($page, $featuredName);
    $this->assertGalleryImagesVisible($page, $galleryNames);
    $this->assertCarouselVisible($page, array_merge([$featuredName], $galleryNames));

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();

it('simulates a full human CRUD lifecycle: create -> show -> modal check -> edit -> show', function (string $theme, bool $useXhr) {
    $title = 'Human CRUD Test ' . uniqid();
    $xhrInt = $useXhr ? 1 : 0;
    $wait = $useXhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    // 1. Start at the index page and go to Create
    $page = $this->visit("/blogs?theme={$theme}&use_xhr={$xhrInt}");
    $page->click('#btn-create-new')
        ->assertSee('Add a new blog');

    // 2. Fill out the form and upload media
    $this->fillBlogForm($page, $title, 'Full lifecycle test content');

    $featuredName = $this->uploadFeaturedImage($page, $this->getRandomFixture());
    
    $galleryNames = [];
    $galleryNames[] = $this->uploadToGallery($page, $this->getRandomFixture());
    $galleryNames[] = $this->uploadToGallery($page, $this->getRandomFixture());

    // 3. Submit the form
    $page->press('#btn-save-blog')
        ->assertSee('Blog created.')
        ->assertSee($title);

    // 4. Verify on Show page
    $this->assertFeaturedImageVisible($page, $featuredName);
    $this->assertGalleryImagesVisible($page, $galleryNames);

    // 5. Modal Carousel Verification
    // Click the 2nd gallery image
    $galleryId = 'blog-gallery-show';
    $galleryItem2Selector = "[data-base-id=\"{$galleryId}\"] [data-mle-media-preview-container]:nth-child(2) [data-mle-media-preview-item]";
    $page->click($galleryItem2Selector);

    // Assert modal is open - use specific ID
    $modalId = "{$galleryId}-mod";
    $modalSelector = $theme === 'bootstrap-5' ? "#{$modalId}.show" : "#{$modalId}.active";
    
    $page->wait(1.0); // Wait for modal to open
    $page->assertPresent($modalSelector);

    // In the modal carousel, check the active item matches image 2
    $activeItemSrc = $page->page()->locator($modalSelector . ' [data-mle-carousel-item].active [data-mle-media-preview-image]')->first()->getAttribute('src');
    $this->assertFilenameMatch($activeItemSrc, $galleryNames[1]);

    // Close modal
    $page->click($modalSelector . ' [data-mle-modal-close]');
    $page->assertMissing($modalSelector);

    // 6. Edit Flow
    $page->click('a.btn-warning') // Edit button on show page
        ->assertSee('Edit blog');

    $newTitle = 'Human CRUD Edit ' . uniqid();
    $page->type('#title', $newTitle);

    // Delete featured image
    $featuredContainer = '[data-base-id="blog-main-inside"]';
    $this->scrollIntoView($page, $featuredContainer);
    $page->click("{$featuredContainer} [data-mle-media-delete-button]");
    $page->assertMissing("{$featuredContainer} [data-mle-media-preview-item]");

    // Upload new featured image
    $newFeaturedName = $this->uploadFeaturedImage($page, $this->getRandomFixture());

    // 7. Save and verify
    $page->press('#btn-update-blog')
        ->assertSee('Blog updated.')
        ->assertSee($newTitle);

    $this->assertFeaturedImageVisible($page, $newFeaturedName);

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();
