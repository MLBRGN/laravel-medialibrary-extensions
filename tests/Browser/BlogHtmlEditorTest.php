<?php

use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Pest\Browser\Api\AwaitableWebpage;

/** @noinspection InvalidDatasetNameCaseInspection */

beforeEach(function () {
    config(['media-library.disk_name' => 'mle_demo_disk']);
});

it('can create a blog using the html editor with custom file picker', function (string $theme, bool $useXhr) {
    if (!class_exists(\Mlbrgn\LaravelFormComponents\Providers\FormComponentsServiceProvider::class)) {
        $this->markTestSkipped('Mlbrgn Form Components not installed.');
    }

    $title = 'HTML Editor Test ' . uniqid();
    $xhrInt = $useXhr ? 1 : 0;
    $wait = $useXhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    // 1. Visit index and click create
    $page = $this->visit("/blogs?theme={$theme}&use_xhr={$xhrInt}");
    $page->click('#btn-create-new')
        ->assertSee('Add a new blog');

    // 2. Fill basic blog info
    $page->type('#title', $title);

    // 3. Interact with HTML Editor (TinyMCE)
    $imageButton = '[data-mce-name="image"]';
    $browseFilesButtonSelector = '[data-mce-name="Browse files"]';
    $saveButtonSelector = '[data-mce-name="Save"]';
    $filePickerIframeSelector = '.tox-dialog-wrap iframe';

    $page->assertPresent($imageButton);
    $this->scrollIntoView($page, $imageButton);

    // Open image dialog
    $page->pressAndWaitFor($imageButton, $wait);
    
    // Open file picker
    $page->pressAndWaitFor($browseFilesButtonSelector, $wait);
    $page->assertPresent($filePickerIframeSelector);

    $uploadedFilename = '';
    $wait = 1.0; // Use stable wait for complex editor interactions

    // Within the file picker iframe
    $page->withinFrame($filePickerIframeSelector, function (AwaitableWebpage $page) use ($wait, &$uploadedFilename) {
        $page->wait(1.0); // Wait for Media Manager to initialize inside iframe
        
        $mediaManagerSelector = '[data-mle-media-manager]';
        $inputSelector = $mediaManagerSelector.' [data-mle-media-input]';
        $uploadButtonSelector = $mediaManagerSelector.' [data-mle-media-upload-button]';
        $firstItemSelectSelector = $mediaManagerSelector.' [data-mle-media-preview-container]:first-child [data-mle-media-select-wrapper]';
        $insertSelectedButtonSelector = '[data-mle-insert-selected]';

        $page->assertPresent($inputSelector);
        
        // Upload an image
        $fixture = $this->getRandomFixture();
        $uploadedFilename = basename($fixture);
        
        $page->attach($inputSelector, $fixture)
            ->pressAndWaitFor($uploadButtonSelector, $wait)
            ->assertSee(__('medialibrary-extensions::messages.upload_success'));

        // Select and Insert
        $page->click($firstItemSelectSelector)
            ->wait($wait)
            ->pressAndWaitFor($insertSelectedButtonSelector, $wait);
    });

    // Back to main page, save the TinyMCE dialog
    $page->pressAndWaitFor($saveButtonSelector, $wait);

    // 4. Upload featured image as well (the "media manager inside the form" part)
    $featuredName = $this->uploadFeaturedImage($page, $this->getRandomFixture());

    // 5. Submit the form
    $page->press('#btn-save-blog')
        ->assertSee('Blog created.')
        ->assertSee($title);

    // 6. Verify promotion and visual presence
    $blog = Blog::where('title', $title)->first();
    $this->assertNotNull($blog);
    
    // Check media counts
    $this->assertCount(1, $blog->getMedia('blog-main'));
    $this->assertCount(1, $blog->getMedia('blog-content'));
    $this->assertDatabaseCount('mle_temporary_uploads', 0);

    // 7. Verify Show page
    $this->assertFeaturedImageVisible($page, $featuredName);
    
    // Check that the HTML content contains an image with the expected filename
    $displaySelector = '#blog-content-display';
    $page->assertPresent($displaySelector);
    
    $htmlContent = $page->page()->locator($displaySelector)->innerHTML();
    $this->assertFilenameMatch($htmlContent, $uploadedFilename);

    $page->page()->close();
})->with('blog_crud_matrix')->group('browser')->flaky();
