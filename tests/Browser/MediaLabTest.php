<?php

/** @noinspection InvalidDatasetNameCaseInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

it('can control media lab', function ($theme, $dataSource, $xhr, $uploadMedia = false) {

    // prepare MMM selectors to upload media first
    $mmmId = '#alien-multiple-permanent-mmm';
    $mmmInputSelector = $mmmId.' [data-mle-media-input]';
    $mmmUploadButtonSelector = $mmmId.' [data-mle-media-upload-button]';

    // prepare media lab selectors
    $labId = '#alien-laboratory-lab';
    $labOriginalSelector = $labId.' [data-mle-media-lab-preview-original]';
    $labBaseSelector = $labId.' [data-mle-media-lab-preview-base]';

    // selectors inside base preview (nested MMS)
    $mmsSelector = $labBaseSelector.' [data-mle-media-manager]';
    $mmsEditButtonSelector = $mmsSelector.' [data-mle-media-edit-button]';

    // selectors for image editor
    $imageEditorModalSelector = $labBaseSelector.' [data-mle-image-editor-modal]';
    $imageEditorModalCloseButtonSelector = $imageEditorModalSelector.' [data-mle-modal-close]';
    $imageEditorModalRotateCcwButtonSelector = $imageEditorModalSelector.' [data-click-action="rotateCcw"]';
    $imageEditorModalSaveButtonSelector = $imageEditorModalSelector.' [data-click-action="save"]';

    // restore button in original preview
    $restoreButtonSelector = $labOriginalSelector.' [data-mle-action="medium-restore"]';

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    // Ensure the Media Lab has a medium to work with, using the correct data source
    $this->ensureLabMedium($dataSource);

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt&section=lab")
        ->assertNoJavaScriptErrors()
        ->assertDontSee('Media lab not showing, no media.');

    $this->scrollIntoView($page, $labId);

    $page->assertPresent($labId)
        ->assertPresent($labOriginalSelector)
        ->assertPresent($labBaseSelector)
        ->assertPresent($mmsSelector)
        ->assertPresent($mmsEditButtonSelector)
        ->assertPresent($restoreButtonSelector);

    // check image editor modal can be opened and closed
    $page->press($mmsEditButtonSelector);
    $this->waitForVisible($page, $imageEditorModalSelector)
        ->assertDontSee(__('medialibrary-extensions::messages.could_not_initialize_image_editor'))
        ->press($imageEditorModalCloseButtonSelector);
    $this->waitForMissing($page, $imageEditorModalSelector);

    // check saving edited image in the image editor
    $page->press($mmsEditButtonSelector);
    $this->waitForVisible($page, $imageEditorModalSelector)
        ->assertDontSee(__('medialibrary-extensions::messages.could_not_initialize_image_editor'))
        ->press($imageEditorModalRotateCcwButtonSelector)
        ->press($imageEditorModalSaveButtonSelector);
    $this->waitForMissing($page, $imageEditorModalSelector);
    
    // Wait for the success message to appear on the page
    \Pest\Browser\Execution::instance()->waitForExpectation(fn () => $page->assertSee(__('medialibrary-extensions::messages.medium_replaced')), 10000);

    // test restore medium
    $page->press($restoreButtonSelector);
    \Pest\Browser\Execution::instance()->waitForExpectation(fn () => $page->assertSee(__('medialibrary-extensions::messages.restored_original')), 10000);

    $page->page()->close();
})->group('browser')
    ->with('media_lab_test_matrix')
    ->flaky();
