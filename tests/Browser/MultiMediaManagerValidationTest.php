<?php

/** @noinspection InvalidDatasetNameCaseInspection */

beforeEach(function () {
    config(['media-library.disk_name' => 'mle_demo_disk']);
});

it('correctly handles validation errors and clears them after successful XHR upload', function (string $theme, bool $useXhr) {
    if (!$useXhr) {
        $this->markTestSkipped('This test specifically targets XHR behavior for clearing errors.');
    }

    $xhrInt = 1;
    
    // 1. Visit Create Blog page
    $page = $this->visit("/blogs/create?theme={$theme}&use_xhr={$xhrInt}");
    $this->waitForMLE($page);
    
    // 2. Fill the form partially (Title empty)
    // and Gallery with 2 images to satisfy its requirement
    $this->uploadToGallery($page, $this->getRandomFixture());
    $this->uploadToGallery($page, $this->getRandomFixture());
    
    // featured_image remains empty (it is required)
    
    // 3. Submit the form
    $page->click('#btn-save-blog');
    
    // 4. Assert validation errors are visible
    // Title is required (standard Laravel error)
    $page->assertSee('The title field is required');
    
    // featured_image is required (Media Manager error)
    // The name passed is 'featured_image'
    // Laravel's default error message for featured_image would be "The featured image field is required."
    // But MinMediaCount rule returns "One medium required" for singles if min=1
    $page->assertSee('One medium required');
    
    // 5. Now upload the featured image via XHR
    $featuredName = $this->uploadFeaturedImage($page, $this->getRandomFixture());
    
    // 6. CHECK IF THE ERROR MESSAGE "One medium required" IS STILL VISIBLE
    $page->assertDontSee('One medium required');

    // Also check for "Uploading disabled" message which confirms the component sees the media
    $page->assertSee(__('medialibrary-extensions::messages.upload_disabled_only_one_medium_allowed'));
    
    // 7. NOW SUBMIT AGAIN WITH MISSING TITLE
    // This is the specific scenario reported by the user.
    // The image was uploaded via XHR, but the title is still empty.
    $page->click('#btn-save-blog');
    
    // WAIT for reload and for JS to be ready
    $this->waitForMLE($page);
    
    // 8. Assert validation errors are visible
    $page->assertSee('The title field is required');
    
    // CRITICAL FIX VERIFICATION: The "One medium required" should NOT reappear
    // if the image was successfully uploaded via XHR and preserved across form submit.
    $page->assertDontSee('One medium required');
    
    // 9. Complete the form and submit
    // Make sure ALL requirements are met (including gallery min 2)
    $title = 'Bug Fix Test ' . uniqid();
    $page->fill('#title', $title);
    
    // We already uploaded 2 images to gallery in step 2.
    // They should still be there.
    
    $page->click('#btn-save-blog');

    $this->waitForMLE($page);

    $page->assertSee('Blog created.')
        ->assertSee($title);

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser');
