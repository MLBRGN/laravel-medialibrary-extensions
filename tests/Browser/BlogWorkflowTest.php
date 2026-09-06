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
    
    // Get the expected filename from the preview image
    $expectedSrc = $page->page()->locator($galleryItem2Selector . ' [data-mle-media-preview-image]')->first()->getAttribute('src');
    $expectedFilename = basename(parse_url($expectedSrc, PHP_URL_PATH));
    $expectedFilenameBase = pathinfo($expectedFilename, PATHINFO_FILENAME);

    $page->click($galleryItem2Selector);

    // Assert modal is open - use specific ID
    $modalId = "{$galleryId}-mod";
    $modalSelector = $theme === 'bootstrap-5' ? "#{$modalId}.show" : "#{$modalId}.active";
    
    $page->assertPresent($modalSelector);

    // In the modal carousel, check the active item matches image 2
    $page->assertPresent($modalSelector . " [data-mle-carousel-item].active [data-mle-media-preview-image][src*='{$expectedFilenameBase}']");

    // Close modal
    $page->click($modalSelector . ' [data-mle-modal-close]');
    $page->assertMissing($modalSelector);

    // 6. Edit Flow
    $page->click('#btn-edit-blog') // Edit button on show page
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

it('allows reordering gallery images during blog creation and maintains sync', function (string $theme, bool $useXhr) {
    $title = 'Reorder Test ' . uniqid();
    $xhrInt = $useXhr ? 1 : 0;
    
    // 1. Start at Create page
    $page = $this->visit("/blogs/create?theme={$theme}&use_xhr={$xhrInt}");
    $page->assertSee('Add a new blog');

    // 2. Fill basic info
    $this->fillBlogForm($page, $title, 'Testing reordering');

    // 3. Upload 3 images to Gallery
    $galleryNames = [];
    $galleryNames[] = $this->uploadToGallery($page, $this->getRandomFixture());
    $galleryNames[] = $this->uploadToGallery($page, $this->getRandomFixture());
    $galleryNames[] = $this->uploadToGallery($page, $this->getRandomFixture());

    // 4. Set the 2nd image as first
    $galleryContainer = '[data-base-id="blog-gallery-outside"]';
    $this->setAsFirst($page, $galleryContainer, 2);

    // Expected new order: [original 2nd, original 1st, original 3rd]
    $expectedOrder = [$galleryNames[1], $galleryNames[0], $galleryNames[2]];

    // 5. Save the blog
    $page->press('#btn-save-blog')
        ->assertSee('Blog created.')
        ->assertSee($title);

    // 6. Verify order on Show page
    $this->assertGalleryImagesVisible($page, $expectedOrder);

    // 7. Verify carousel sync on Show page for the NEW first item
    $galleryId = 'blog-gallery-show';
    $firstItemSelector = "[data-base-id=\"{$galleryId}\"] [data-mle-media-preview-container]:first-child [data-mle-media-preview-item]";
    $page->click($firstItemSelector);

    $modalId = "{$galleryId}-mod";
    $modalSelector = $theme === 'bootstrap-5' ? "#{$modalId}.show" : "#{$modalId}.active";
    
    $page->assertPresent($modalSelector);
    $activeItemSrc = $page->page()->locator($modalSelector . ' [data-mle-carousel-item].active [data-mle-media-preview-image]')->first()->getAttribute('src');
    $this->assertFilenameMatch($activeItemSrc, $expectedOrder[0]);

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();
