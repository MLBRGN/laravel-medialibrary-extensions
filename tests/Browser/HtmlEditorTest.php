<?php

/** @noinspection InvalidDatasetNameCaseInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

use Illuminate\Support\Facades\Config;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;
use Pest\Browser\Api\AwaitableWebpage;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

it('can control html editor\'s custom file picker', function ($theme, $dataSource, $xhr, $uploadMedia = false) {

    // keep small to speed up test and make intent clear
    Config::set('medialibrary-extensions.max_items_in_shared_media_collections', 2);

    $imageButton = '[data-mce-name="image"]';
    $saveButtonSelector = '[data-mce-name="Save"]';
    $cancelButtonSelector = '[data-mce-name="Cancel"]';
    $browseFilesButtonSelector = '[data-mce-name="Browse files"]';

    // tinyMCE selectors
    $iframeSelector = '.tox-dialog-wrap iframe';

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $page->assertSee('Mlbrgn Form components custom file picker integration');

    $page->assertPresent($imageButton);
    $this->scrollIntoView($page, $imageButton);

    // open image picker and cancel
    $page->pressAndWaitFor($imageButton, $waitTime);
    $page->assertPresent($browseFilesButtonSelector);
    $page->assertPresent($saveButtonSelector);
    $page->assertPresent($cancelButtonSelector);
    $page->pressAndWaitFor($cancelButtonSelector, $waitTime);

    // open the image picker and open the file picker
    $page->pressAndWaitFor($imageButton, $waitTime);
    $page->pressAndWaitFor($browseFilesButtonSelector, $waitTime);
    $page->assertPresent('.tox-dialog-wrap');
    $page->assertPresent($iframeSelector);

    $page->withinFrame($iframeSelector, function (AwaitableWebpage $page) use ($waitTime) {

        $page->wait(0.5);

        // prepare selectors
        $mediaManagerId = '#media-manager-mmm';
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
        $imageEditorModalRotateCcwButtonSelector = $imageEditorModalSelector.' [data-click-action="rotateCcw"]';
        $imageEditorModalSaveButtonSelector = $imageEditorModalSelector.' [data-click-action="save"]';

        $carouselId = '#media-manager-mod-crs';
        $indicatorsSelector = $carouselId.' [data-mle-carousel-indicators]';
        $nextButtonSelector = $carouselId.' [data-mle-carousel-next]';
        $prevButtonSelector = $carouselId.' [data-mle-carousel-prev]';
        $firstItemSelector = $carouselId.' [data-mle-carousel-item]:first-child';
        $secondItemSelector = $carouselId.' [data-mle-carousel-item]:nth-child(2)';

        $page->assertPresent($inputSelector)
            ->assertPresent($uploadButtonSelector);

        // test that it shows error when no file selected
        $page->pressAndWaitFor($uploadButtonSelector, $waitTime)
            ->assertSee(__('medialibrary-extensions::messages.upload_no_files'));

        // test that invalid mime types are rejected
        $page->attach($inputSelector, $this->getInvalidMimeTypeFixture())
            ->pressAndWaitFor($uploadButtonSelector, $waitTime)
            ->assertSee(__('medialibrary-extensions::messages.upload_failed_due_to_invalid_mimetype'));

        $maxItems = config('medialibrary-extensions.max_items_in_shared_media_collections');

        $page->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => 0, 'total' => $maxItems]));

        for ($i = 0; $i < $maxItems; $i++) {
            // attach an image file and submit and check if spinner shows and upload is successful
            $page->attach($inputSelector, $this->getRandomFixture())
                ->pressAndWaitFor($uploadButtonSelector, $waitTime)
                ->assertSee(__('medialibrary-extensions::messages.please_wait'))
                ->assertSee(__('medialibrary-extensions::messages.upload_success'));

            // counts should update
            $page->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => $i + 1, 'total' => $maxItems]));
        }

        // counts should reflect max, and upload should be disabled with an alert when at max
        $page->assertPresent($maxReachedAlertSelector);

        // assert that the image is visible in the preview
        $page->assertPresent($gridSelector.' [data-mle-media-preview-item]:first-child')

            // assert grid is present
            ->assertPresent($gridSelector)

            // assert grid has the media container
            ->assertPresent($firstMediaPreviewContainer)

            // check that the media item's menu has the expected buttons and state
            ->assertButtonEnabled($editButtonSelector)
            ->assertButtonDisabled($setAsFirstButtonSelector)
            ->assertButtonEnabled($deleteButtonSelector);

        // check media modal opening and presence of expected elements
        $page->assertPresent($mediaPreviewImageSelector)
            ->pressAndWaitFor($mediaPreviewImageSelector, $waitTime)

            ->assertPresent($mediaModalSelector)
            ->assertPresent($mediaModalCloseButtonSelector)
            ->assertPresent($mediaModalCarouselSelector)
            ->assertPresent($mediaModalCarouselIndicatorSelector)
            ->assertPresent($mediaModalCarouselItemSelector);

        $page->assertPresent($carouselId)
            ->assertPresent($indicatorsSelector)
            ->assertPresent($nextButtonSelector)
            ->assertPresent($prevButtonSelector)
            ->assertPresent($firstItemSelector);

        // check that media modal can be closed
        $page->pressAndWaitFor($mediaModalCloseButtonSelector, $waitTime);

        // check that image editor custom element is registered
        expect(
            $page->script("customElements.get('image-editor') !== undefined")
        )->toBeTrue();

        expect(
            $page->script("document.querySelector('script[src*=\"modal-image-editor.js\"]') !== null")
        )->toBeTrue();

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
            ->assertSee(__('medialibrary-extensions::messages.medium_removed'));

        // select the first item
        $firstItemSelectSelector = $firstMediaPreviewContainer.' [data-mle-media-select-wrapper]';
        $page->assertPresent($firstItemSelectSelector);
        $page->click($firstItemSelectSelector);
        $page->wait($waitTime);

        // click insert selected media
        $insertSelectedButtonSelector = '[data-mle-insert-selected]';
        $page->pressAndWaitFor($insertSelectedButtonSelector, $waitTime);
        $page->wait($waitTime);

    });

    $page->pressAndWaitFor($saveButtonSelector, $waitTime);
    $page->wait($waitTime);

    $tinyMceIframeSelector = '.tox-edit-area__iframe';
    $page->assertPresent($tinyMceIframeSelector);
    $page->withinFrame($tinyMceIframeSelector, function (AwaitableWebpage $page) {
        $tinyMceBodySelector = '#tinymce';
        $page->assertPresent($tinyMceBodySelector);
        $tinyMceBodyImgSelector = $tinyMceBodySelector.' img';
        $page->assertPresent($tinyMceBodyImgSelector);
    });

    $page->page()->close();
})->group('browser')
    ->with('media_html_editor_matrix');
