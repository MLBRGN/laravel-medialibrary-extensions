<?php

/** @noinspection InvalidDatasetNameCaseInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

use Illuminate\Support\Facades\Config;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;
use Mlbrgn\MediaLibraryExtensions\Tests\Browser\Concerns\InteractsWithBlogIntegration;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

uses(InteractsWithBlogIntegration::class);

it('can control mmm', function ($theme, $dataSource, $xhr, $storage) {

    Config::set('medialibrary-extensions.max_items_in_shared_media_collections', 3);

    // prepare selectors
    $mediaManagerId = '#alien-multiple-'.$storage.'-mmm';
    $inputSelector = $mediaManagerId.' [data-mle-media-input]';
    $uploadButtonSelector = $mediaManagerId.' [data-mle-media-upload-button]';
    $countsSelector = $mediaManagerId.' .mle-media-manager-media-counts';
    $maxReachedAlertSelector = $mediaManagerId.' [data-mle-max-reached-alert]';
    $gridSelector = $mediaManagerId.' [data-mle-media-preview-grid]';
    $firstMediaPreviewContainer = $gridSelector.' [data-mle-media-preview-container]:first-child';
    $editButtonSelector = $firstMediaPreviewContainer.' [data-mle-media-edit-button]';
    $setAsFirstButtonSelector = $firstMediaPreviewContainer.' [data-mle-media-set-as-first-button]';
    $deleteButtonSelector = $firstMediaPreviewContainer.' [data-mle-media-delete-button]';

    // for modal testing
    $mediaPreviewItemSelector = $firstMediaPreviewContainer.' [data-mle-media-preview-item]';
    $mediaPreviewImageSelector = $mediaPreviewItemSelector.' [data-mle-media-preview-image]';
    $mediaModalSelector = $mediaManagerId.' [data-mle-media-modal]';
    $mediaModalCloseButtonSelector = $mediaModalSelector.' [data-mle-modal-close]';

    // for modal carousel testing
    $mediaModalCarouselSelector = $mediaModalSelector.' [data-mle-carousel]';
    $mediaModalCarouselIndicatorSelector = $mediaModalCarouselSelector.' [data-mle-carousel-indicators]';
    $mediaModalCarouselItemSelector = $mediaModalCarouselSelector.' [data-mle-carousel-item]';

    // for image editor modal testing
    $imageEditorModalSelector = $firstMediaPreviewContainer.' [data-mle-image-editor-modal]';
    $imageEditorModalCloseButtonSelector = $imageEditorModalSelector.' [data-mle-modal-close]';
    $imageEditorModalSaveButtonSelector = $imageEditorModalSelector.' [data-click-action="save"]';
    $imageEditorModalRotateCcwButtonSelector = $imageEditorModalSelector.' [data-click-action="rotateCcw"]';

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    $dataSourceResolver = app(DataSourceResolver::class);
    $resolvedConnection = $dataSourceResolver->resolveConnection($dataSource);

    $this->assertDatabaseCount('media', 0, $resolvedConnection);
    $this->assertDatabaseCount('mle_temporary_uploads', 0, $resolvedConnection);

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    // check that image editor custom element is registered
    $page->page()->waitForFunction("customElements.get('image-editor') !== undefined");

    $this->scrollIntoView($page, $mediaManagerId);

    // assert that the upload button is initially enabled
    $page->assertPresent($inputSelector)
        ->assertButtonEnabled($uploadButtonSelector);

    // test that it shows error when no file selected
    $page->press($uploadButtonSelector)
        ->assertSee(__('medialibrary-extensions::messages.upload_no_files'));

    // test that invalid mime types are rejected
    $page->attach($inputSelector, $this->getInvalidMimeTypeFixture())
        ->press($uploadButtonSelector);
    
    if (!$xhr) {
        $page->wait($waitTime); // Wait for redirect/session to settle
    }

    $page->assertSee(__('medialibrary-extensions::messages.upload_failed_due_to_invalid_mimetype'));

    $maxItems = config('medialibrary-extensions.max_items_in_shared_media_collections');

    $page->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => 0, 'total' => $maxItems]));

    for ($i = 0; $i < $maxItems; $i++) {
        // attach an image file and submit and check if spinner shows and upload is successful
        $page->attach($inputSelector, $this->getRandomFixture())
            ->press($uploadButtonSelector);

        if (!$xhr) {
            $page->wait($waitTime);
        }

        $page->assertSee(__('medialibrary-extensions::messages.upload_success'));

        // counts should update
        $page->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => $i + 1, 'total' => $maxItems]));
    }

    // counts should reflect max, and upload should be disabled with an alert when at max
    $page->assertPresent($maxReachedAlertSelector);

    // assert that the image is visible in the preview
    $page->assertPresent($gridSelector.' [data-mle-media-preview-item]:first-child')
        ->assertButtonDisabled($uploadButtonSelector)
        ->assertPresent($gridSelector)
        ->assertPresent($firstMediaPreviewContainer);

    // check that the media item's menu has the expected buttons and state
    $page->assertButtonEnabled($editButtonSelector)
        ->assertButtonDisabled($setAsFirstButtonSelector)
        ->assertButtonEnabled($deleteButtonSelector)

    // check media modal opening and presence of expected elements
        ->assertPresent($mediaPreviewImageSelector)
        ->press($mediaPreviewImageSelector)

        ->assertPresent($mediaModalSelector)
        ->assertPresent($mediaModalCloseButtonSelector)
        ->assertPresent($mediaModalCarouselSelector)
        ->assertPresent($mediaModalCarouselIndicatorSelector)
        ->assertPresent($mediaModalCarouselItemSelector)

    // check that media modal can be closed
        ->press($mediaModalCloseButtonSelector)
        ->assertMissing($mediaModalSelector);

    // check that the carousel shows the correct images for multiple items (random check)
    for ($i = 0; $i < 2; $i++) {
        $randomIndex = rand(1, $maxItems);
        $currentSelector = $gridSelector." [data-mle-media-preview-container]:nth-child({$randomIndex}) [data-mle-media-preview-item]";

        // Get the src of the preview image
        $previewSrc = $page->page()->locator($currentSelector . ' [data-mle-media-preview-image]')->first()->getAttribute('src');
        $filenamePart = basename(parse_url($previewSrc, PHP_URL_PATH));

        $page->click($currentSelector);
        // Wait for modal to become visible
        $page->assertVisible($mediaModalSelector);

        // Verify active slide matches the clicked image
        $page->assertPresent($mediaModalSelector . ' [data-mle-carousel-item].active [data-mle-media-preview-image][src*="' . $filenamePart . '"]');

        // Close modal
        $page->click($mediaModalCloseButtonSelector);
        $page->assertMissing($mediaModalSelector);
    }

    // check image editor modal can be opened and closed
    $page->press($editButtonSelector)
        ->assertPresent($imageEditorModalSelector)
        ->assertDontSee(__('medialibrary-extensions::messages.could_not_initialize_image_editor'))
        ->press($imageEditorModalCloseButtonSelector)
        ->wait(0.5)
        ->assertMissing($imageEditorModalSelector);

    // check saving edited image in the image editor
    $page->press($editButtonSelector)
        ->assertVisible($imageEditorModalSelector)
        ->assertDontSee(__('medialibrary-extensions::messages.could_not_initialize_image_editor'))
        ->press($imageEditorModalRotateCcwButtonSelector)
        ->press($imageEditorModalSaveButtonSelector)
        ->wait(0.5)
        ->assertMissing($imageEditorModalSelector);

    // delete one media and validate counts/alerts/form state
    $page->wait($waitTime) // Wait for previous redirect/DOM to settle
        ->press($deleteButtonSelector)
        ->assertSee(__('medialibrary-extensions::messages.medium_removed'))
        ->assertMissing($maxReachedAlertSelector)
        ->assertButtonEnabled($uploadButtonSelector);

    $remaining = $maxItems - 1;
    // delete the rest to ensure stability of the delete flow
    for ($i = 0; $i < $remaining - 1; $i++) {
        $currentDeleteButtonSelector =
            $gridSelector.
            ' [data-mle-media-preview-container]:first-child [data-mle-media-delete-button]';
        $page->press($currentDeleteButtonSelector)
            ->assertSee(__('medialibrary-extensions::messages.medium_removed'));

        $page->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => $maxItems - $i - 2, 'total' => $maxItems]));
    }

    // the upload button should be enabled again
    $page->assertButtonEnabled($uploadButtonSelector);

    $page->page()->close();
})->group('browser')
    ->with('mmm_test_matrix')
    ->flaky();

it('can use set-as-first and carousel remains synced', function ($theme, $dataSource, $xhr, $storage) {
    // prepare selectors
    $mediaManagerId = '#alien-multiple-'.$storage.'-mmm';
    $inputSelector = $mediaManagerId.' [data-mle-media-input]';
    $uploadButtonSelector = $mediaManagerId.' [data-mle-media-upload-button]';
    $gridSelector = $mediaManagerId.' [data-mle-media-preview-grid]';

    $mediaModalSelector = $mediaManagerId.' [data-mle-media-modal]';
    $mediaModalCloseButtonSelector = $mediaModalSelector.' [data-mle-modal-close]';

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $mediaManagerId);

    // 1. Upload 3 images
    for ($i = 0; $i < 3; $i++) {
        $page->attach($inputSelector, $this->getRandomFixture())
            ->press($uploadButtonSelector);

        if (!$xhr) {
            $page->wait($waitTime);
        }

        $page->assertSee(__('medialibrary-extensions::messages.upload_success'));
    }

    // 2. Refresh the page to ensure correct order/state and clean DOM
    $page->refresh();
    $this->scrollIntoView($page, $mediaManagerId);

    // 3. Verify initial synchronization of the SECOND item
    $secondContainer = $gridSelector.' [data-mle-media-preview-container]:nth-child(2)';
    $secondItemPreview = $secondContainer.' [data-mle-media-preview-item]';
    $secondItemImage = $secondItemPreview.' [data-mle-media-preview-image]';

    $secondSrc = $page->page()->locator($secondItemImage)->first()->getAttribute('src');
    $secondFilename = basename(parse_url($secondSrc, PHP_URL_PATH));
    $secondFilenameBase = pathinfo($secondFilename, PATHINFO_FILENAME);

    $page->click($secondItemPreview)
        ->assertVisible($mediaModalSelector)
        // Wait until the active slide matches the clicked image
        ->assertPresent($mediaModalSelector . " [data-mle-carousel-item].active [data-mle-media-preview-image][src*='{$secondFilenameBase}']")
        ->click($mediaModalCloseButtonSelector)
        ->assertMissing($mediaModalSelector);

    // 4. Set the second item as first
    $setAsFirstButtonSelector = $secondContainer.' [data-mle-media-set-as-first-button]';
    $page->press($setAsFirstButtonSelector);

    if (!$xhr) {
        $page->wait($waitTime);
    }

    $page->assertSee(__('medialibrary-extensions::messages.medium_set_as_main'));

    // 5. Verify it is now first in the grid
    $firstContainer = $gridSelector.' [data-mle-media-preview-container]:first-child';
    $firstItemImage = $firstContainer.' [data-mle-media-preview-image]';
    $newFirstSrc = $page->page()->locator($firstItemImage)->first()->getAttribute('src');
    $this->assertFilenameMatch($newFirstSrc, $secondFilename);

    // 6. Verify synchronization of the NEW first item
    $page->click($firstContainer.' [data-mle-media-preview-item]')
        ->assertVisible($mediaModalSelector)
        ->assertPresent($mediaModalSelector . " [data-mle-carousel-item].active [data-mle-media-preview-image][src*='{$secondFilenameBase}']")
        ->click($mediaModalCloseButtonSelector)
        ->assertMissing($mediaModalSelector);

    // 7. Verify synchronization of the NEW second item (which was the first)
    $newSecondContainer = $gridSelector.' [data-mle-media-preview-container]:nth-child(2)';
    $newSecondItemImage = $newSecondContainer.' [data-mle-media-preview-image]';
    $newSecondSrc = $page->page()->locator($newSecondItemImage)->first()->getAttribute('src');
    $newSecondFilename = basename(parse_url($newSecondSrc, PHP_URL_PATH));
    $newSecondFilenameBase = pathinfo($newSecondFilename, PATHINFO_FILENAME);

    $page->click($newSecondContainer.' [data-mle-media-preview-item]')
        ->assertVisible($mediaModalSelector)
        ->assertPresent($mediaModalSelector . " [data-mle-carousel-item].active [data-mle-media-preview-image][src*='{$newSecondFilenameBase}']")
        ->click($mediaModalCloseButtonSelector)
        ->assertMissing($mediaModalSelector);

    $page->page()->close();
})->group('browser')
    ->with('mmm_test_matrix')
    ->flaky();

it('enforces max items cap on multiple media manager (mmm) on demo page', function ($theme, $dataSource, $storage) {

    // keep small to speed up test and make intent clear
    Config::set('medialibrary-extensions.max_items_in_shared_media_collections', 2);

    $mediaManagerId = '#alien-multiple-'.$storage.'-mmm';
    $inputSelector = $mediaManagerId.' [data-mle-media-input]';
    $uploadButtonSelector = $mediaManagerId.' [data-mle-media-upload-button]';
    $countsSelector = $mediaManagerId.' .mle-media-manager-media-counts';
    $gridSelector = $mediaManagerId.' [data-mle-media-preview-grid]';

    $xhrInt = 1; // focused on XHR for stability
    $waitTime = $this->waitTimeXhr;

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $mediaManagerId);

    // initial state (avoid assuming starting count; just ensure controls are present)
    $page->assertPresent($inputSelector)
        ->assertPresent($uploadButtonSelector)
        ->assertPresent($countsSelector);

    // upload until cap is reached
    $maxItems = 2; // Fixed for this test context
    for ($i = 1; $i <= $maxItems; $i++) {
        $page->attach($inputSelector, $this->getRandomFixture())
            ->press($uploadButtonSelector)
            // Wait for counts to update which indicates upload finished and JS processed it
            ->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => $i, 'total' => $maxItems]));
    }

    // at cap: button should be disabled
    $page->assertButtonDisabled($uploadButtonSelector);

    // attempt to exceed cap should not add another preview item
    $thirdItemSelector = $gridSelector.' [data-mle-media-preview-container]:nth-child(3)';
    $page->attach($inputSelector, $this->getRandomFixture());
    // even if we click, UI should keep disabled; guard with a presence check
    $page->assertButtonDisabled($uploadButtonSelector)
        ->assertMissing($thirdItemSelector)
        ->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => 2, 'total' => 2]));

    $page->page()->close();
})->group('browser')
    ->with('mmm_cap_matrix');
