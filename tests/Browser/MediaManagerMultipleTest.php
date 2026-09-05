<?php

/** @noinspection InvalidDatasetNameCaseInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

use Illuminate\Support\Facades\Config;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

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
    $mediaModalSelector = $firstMediaPreviewContainer.' [data-mle-media-modal]';
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
    expect(
        $page->script("customElements.get('image-editor') !== undefined")
    )->toBeTrue();

    $this->scrollIntoView($page, $mediaManagerId);

    // assert that the upload button is initially enabled
    $page->assertPresent($inputSelector)
        ->assertButtonEnabled($uploadButtonSelector);

    // test that it shows error when no file selected
    $page->pressAndWaitFor($uploadButtonSelector, $waitTime);

    $page->assertSee(__('medialibrary-extensions::messages.upload_no_files'));

    // test that invalid mime types are rejected
    $page->attach($inputSelector, $this->getInvalidMimeTypeFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.upload_failed_due_to_invalid_mimetype'));

    $maxItems = config('medialibrary-extensions.max_items_in_shared_media_collections');

    $page->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => 0, 'total' => $maxItems]));

    for ($i = 0; $i < $maxItems; $i++) {
        // attach an image file and submit and check if spinner shows and upload is successful
        $page->attach($inputSelector, $this->getRandomFixture());
        $page->pressAndWaitFor($uploadButtonSelector, $waitTime)
            ->assertSee(__('medialibrary-extensions::messages.please_wait'))
            ->assertSee(__('medialibrary-extensions::messages.upload_success'));

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
        ->pressAndWaitFor($mediaPreviewImageSelector, $waitTime)

        ->assertPresent($mediaModalSelector)
        ->assertPresent($mediaModalCloseButtonSelector)
        ->assertPresent($mediaModalCarouselSelector)
        ->assertPresent($mediaModalCarouselIndicatorSelector)
        ->assertPresent($mediaModalCarouselItemSelector)

    // check that media modal can be closed
        ->pressAndWaitFor($mediaModalCloseButtonSelector, $waitTime);

    // check image editor modal can be opened and closed
    $page->pressAndWaitFor($editButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSelector)
        ->assertDontSee(__('medialibrary-extensions::messages.could_not_initialize_image_editor'))
        ->pressAndWaitFor($imageEditorModalCloseButtonSelector, $waitTime);

    // check saving edited image in the image editor
    $page->pressAndWaitFor($editButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSelector)
        ->assertVisible($imageEditorModalSelector)
        ->assertDontSee(__('medialibrary-extensions::messages.could_not_initialize_image_editor'))
        ->pressAndWaitFor($imageEditorModalRotateCcwButtonSelector, $waitTime)
        ->pressAndWaitFor($imageEditorModalSaveButtonSelector, $waitTime)
        ->assertMissing($imageEditorModalSelector);

    // delete one media and validate counts/alerts/form state
    $page->pressAndWaitFor($deleteButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.please_wait'))
        ->assertSee(__('medialibrary-extensions::messages.medium_removed'))
        ->assertMissing($maxReachedAlertSelector)
        ->assertButtonEnabled($uploadButtonSelector);

    $remaining = $maxItems - 1;
    // delete the rest to ensure stability of the delete flow
    for ($i = 0; $i < $remaining - 1; $i++) {
        $currentDeleteButtonSelector =
            $gridSelector.
            ' [data-mle-media-preview-container]:first-child [data-mle-media-delete-button]';
        $page->pressAndWaitFor($currentDeleteButtonSelector, $waitTime);
        $page->assertSee(__('medialibrary-extensions::messages.please_wait'))
            ->assertSee(__('medialibrary-extensions::messages.medium_removed'));

        $page->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => $maxItems - $i - 2, 'total' => $maxItems]));
    }

    // the upload button should be enabled again
    $page->assertButtonEnabled($uploadButtonSelector);

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

    // upload until cap is reached (without assuming initial count)
    for ($i = 1; $i <= 3; $i++) {
        // if the button is already disabled, stop trying to upload
        try {
            $page->assertButtonEnabled($uploadButtonSelector);
        } catch (Throwable $e) {
            break;
        }

        $page->attach($inputSelector, $this->getRandomFixture())
            ->pressAndWaitFor($uploadButtonSelector, $waitTime)
            ->wait($waitTime)
            ->assertPresent($countsSelector);
    }

    // at cap: button should be disabled (allow a short wait for DOM to settle)
    $page->wait($waitTime)
        ->assertButtonDisabled($uploadButtonSelector);

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
