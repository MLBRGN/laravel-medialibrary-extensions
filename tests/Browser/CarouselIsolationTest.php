<?php

use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

it('does not show temporary uploads in carousels by default when model is permanent', function (string $theme, bool $useXhr) {
    $xhrInt = $useXhr ? 1 : 0;
    
    // 1. Create a blog
    $blog = Blog::create(['title' => 'Existing Blog', 'content' => 'Some content']);
    
    // 2. Visit Blog Create page and upload a temporary image
    $page = $this->visit("/blogs/create?theme={$theme}&use_xhr={$xhrInt}");
    $this->waitForMLE($page);
    $page->assertSee('Add a new blog');
    
    // We use a specific ID from the create form for a temporary upload
    $mmsId = '#blog-main-mms';
    $this->uploadFeaturedImage($page, $this->getRandomFixture(), $mmsId);
    
    // Verify there is one temporary upload in DB
    $this->assertDatabaseCount('mle_temporary_uploads', 1);
    
    // 3. Navigate to the Show page of the EXISTING blog
    // The carousel there is NOT configured to show temporary uploads
    $page->navigate("/blogs/{$blog->id}?theme={$theme}&use_xhr={$xhrInt}");
    $page->assertSee('Existing Blog');
    
    $carouselId = '#blog-carousel-show-crs';
    $page->assertPresent($carouselId)
        ->assertSee(__('medialibrary-extensions::messages.no_media'));
        
    // 4. Verify that the temporary upload is still there in DB (it wasn't promoted or deleted)
    $this->assertDatabaseCount('mle_temporary_uploads', 1);

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser');

it('shows temporary uploads in carousels when explicitly configured', function (string $theme, bool $useXhr) {
    $xhrInt = $useXhr ? 1 : 0;
    
    // 1. Visit demo page
    // The demo carousel is configured with 'includeTemporaryUploads' => true
    $page = $this->visit("/mle-demo?theme={$theme}&data_source=demo_default&use_xhr={$xhrInt}");
    $this->waitForMLE($page);
    
    // 2. Upload a temporary image to MMM (alien-gallery)
    $mmmId = '#alien-multiple-temporary-mmm';
    $input = $mmmId . ' [data-mle-media-input]';
    $btn = $mmmId . ' [data-mle-media-upload-button]';
    
    $fixture = $this->getRandomFixture();
    $filename = basename($fixture);
    
    $this->scrollIntoView($page, $mmmId);
    $page->attach($input, $fixture)
        ->click($btn)
        ->assertSee(__('medialibrary-extensions::messages.upload_success'));
        
    // 3. Refresh to update carousel (or check if it's there)
    $page->refresh();
    
    $carouselId = '#alien-carousel-crs';
    $this->scrollIntoView($page, $carouselId);
    
    // The demo carousel SHOULD show the temporary upload
    $page->assertPresent($carouselId)
        ->assertPresent($carouselId . ' [data-mle-carousel-item]');
    
    $src = $page->page()->locator($carouselId . ' [data-mle-carousel-item] img')->first()->getAttribute('src');
    expect($src)->toContain('media_temporary');

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser');
