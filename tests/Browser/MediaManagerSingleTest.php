<?php

/** @noinspection InvalidDatasetNameCaseInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

it('can control mms', function ($theme, $dataSource, $xhr, $storage) {

    // prepare selectors
    $mediaManagerId = '#alien-single-'.$storage.'-mms';
    $inputSelector = $mediaManagerId.' [data-mle-media-input]';
    $uploadButtonSelector = $mediaManagerId.' [data-mle-media-upload-button]';
    $uploadButtonYouTubeSelector = $mediaManagerId.' [data-mle-youtube-upload-button]';

    $countsSelector = $mediaManagerId.' .mle-media-manager-media-counts';
    $maxReachedAlertSelector = $mediaManagerId.' [data-mle-max-reached-alert]';
    $gridSelector = $mediaManagerId.' [data-mle-media-preview-grid]';
    $firstMediaPreviewContainer = $gridSelector.' [data-mle-media-preview-container]:first-child';
    $editButtonSelector = $firstMediaPreviewContainer.' [data-mle-media-edit-button]';
    $setAsFirstButtonSelector = $firstMediaPreviewContainer.' [data-mle-media-set-as-first-button]';
    $deleteButtonSelector = $firstMediaPreviewContainer.' [data-mle-media-delete-button]';

    // for media modal testing
    $mediaPreviewItemSelector = $firstMediaPreviewContainer.' [data-mle-media-preview-item]';
    $mediaPreviewImageSelector = $mediaPreviewItemSelector.' [data-mle-media-preview-image]';
    $mediaModalSelector = $firstMediaPreviewContainer.' [data-mle-media-modal]';
    $mediaModalCloseButtonSelector = $mediaModalSelector.' [data-mle-modal-close]';

    // for modal carousel testing
    $mediaModalCarouselSelector = $mediaModalSelector.' [data-mle-carousel]';
    $mediaModalCarouselIndicatorSelector = $mediaModalCarouselSelector.' [data-mle-carousel-indicators]';
    $mediaModalCarouselItemSelector = $mediaModalCarouselSelector.' [data-mle-carousel-item]';
    $mediaModalCarouselItemContainerSelector = $mediaModalCarouselItemSelector.' .mle-media-carousel-item-container';
    $mediaModalCarouselItemContainerImageSelector = $mediaModalCarouselItemContainerSelector.' img';

    // for image editor modal testing
    $imageEditorModalSelector = $firstMediaPreviewContainer.' [data-mle-image-editor-modal]';
    $imageEditorModalCloseButtonSelector = $imageEditorModalSelector.' [data-mle-modal-close]';
    $imageEditorModalSaveButtonSelector = $imageEditorModalSelector.' [data-click-action="save"]';
    $imageEditorModalCancelButtonSelector = $imageEditorModalSelector.' [data-click-action="cancel"]';
    $imageEditorModalRotateCcwButtonSelector = $imageEditorModalSelector.' [data-click-action="rotateCcw"]';

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    $dataSourceResolver = app(DataSourceResolver::class);
    $resolvedConnection = $dataSourceResolver->resolveConnection($dataSource);

    $this->assertDatabaseCount('media', 0, $resolvedConnection);
    $this->assertDatabaseCount('mle_temporary_uploads', 0, $resolvedConnection);

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $mediaManagerId);

    $page->assertPresent($inputSelector)

        // assert that the upload button is initially enabled
        ->assertButtonEnabled($uploadButtonSelector);

    // check counts start at 0 of 1
    $page->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => 0, 'total' => 1]));

    // test that it shows error when no file selected
    $page->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.upload_no_files'));

        // test that invalid mime types are rejected
    $page->attach($inputSelector, $this->getInvalidMimeTypeFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.upload_failed_due_to_invalid_mimetype'))

        // attach an image file and submit and check if spinner shows and upload is successful
        ->attach($inputSelector, $this->getRandomFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.please_wait'))
        ->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // counts should update
    $page->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => 1, 'total' => 1]));

    $page->assertPresent($maxReachedAlertSelector);

    // assert that the image is visible in the preview
    $page->assertPresent($gridSelector.' [data-mle-media-preview-item]:first-child');

    // assert that the upload button is disabled after upload (single media)
    $page->assertButtonDisabled($uploadButtonSelector);
    $page->assertButtonDisabled($uploadButtonYouTubeSelector);

    // assert grid is present
    $page->assertPresent($gridSelector);

    // assert grid has the media container
    $page->assertPresent($firstMediaPreviewContainer);

    // check that the media item's menu has the expected buttons and state
    $page->assertButtonEnabled($editButtonSelector)
        ->assertButtonDisabled($setAsFirstButtonSelector)
        ->assertButtonEnabled($deleteButtonSelector);

    // check media modal opening and presence of expected elements
    $page->assertPresent($mediaPreviewImageSelector)
        ->pressAndWaitFor($mediaPreviewImageSelector, $waitTime)
        ->assertPresent($mediaModalSelector)
        ->assertPresent($mediaModalCloseButtonSelector)
        ->assertPresent($mediaModalCarouselSelector)
        ->assertPresent($mediaModalCarouselIndicatorSelector)
        ->assertPresent($mediaModalCarouselItemSelector)
        ->assertPresent($mediaModalCarouselItemContainerSelector)
        ->assertPresent($mediaModalCarouselItemContainerImageSelector)

       // Check that the media modal can be closed using the close button
        ->pressAndWaitFor($mediaModalCloseButtonSelector, $waitTime);

    // check image editor modal can be closed using the close button
    $page->pressAndWaitFor($editButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSelector)
        ->assertVisible($imageEditorModalSelector)
        ->assertDontSee(__('medialibrary-extensions::messages.could_not_initialize_image_editor'))
        ->pressAndWaitFor($imageEditorModalCloseButtonSelector, $waitTime)
        ->assertMissing($imageEditorModalSelector);

    // check saving edited image in the image editor
    $page->pressAndWaitFor($editButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSelector)
        ->assertVisible($imageEditorModalSelector)
        ->assertDontSee(__('medialibrary-extensions::messages.could_not_initialize_image_editor'))
        ->pressAndWaitFor($imageEditorModalRotateCcwButtonSelector, $waitTime)
        ->pressAndWaitFor($imageEditorModalSaveButtonSelector, $waitTime)
        ->assertMissing($imageEditorModalSelector);

    // check canceling image editing in the image editor
    $page->pressAndWaitFor($editButtonSelector, $waitTime)
        ->assertVisible($imageEditorModalSelector)
        ->assertDontSee(__('medialibrary-extensions::messages.could_not_initialize_image_editor'))
        ->pressAndWaitFor($imageEditorModalCancelButtonSelector, $waitTime)
        ->assertMissing($imageEditorModalSelector);

    // check delete media works
    $page->pressAndWaitFor($deleteButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.please_wait'))
        ->assertSee(__('medialibrary-extensions::messages.medium_removed'));

    // the upload button should be enabled again
    $page->assertButtonEnabled($uploadButtonSelector);

    // max alert should be gone after XHR delete
    $page->assertMissing($maxReachedAlertSelector);

    $page->page()->close();
})->group('browser')
    ->with('mms_test_matrix')
    ->flaky();

it('honors min / max width height and file size constraints in uploads', function ($theme, $dataSource, $xhr, $storage) {

    // prepare selectors
    $mediaManagerId = '#alien-single-'.$storage.'-mms';
    $inputSelector = $mediaManagerId.' [data-mle-media-input]';
    $uploadButtonSelector = $mediaManagerId.' [data-mle-media-upload-button]';

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $mediaManagerId);

    $page->assertPresent($inputSelector)

        // assert that the upload button is initially enabled
        ->assertButtonEnabled($uploadButtonSelector);

    config(['medialibrary-extensions.max_image_width' => 1500]);
    config(['medialibrary-extensions.max_image_height' => 1500]);

    // test that an image that is too small is rejected
    $page->attach($inputSelector, $this->getTinyImageFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.image_too_small', ['width' => 16, 'height' => 16, 'min_width' => config('medialibrary-extensions.min_image_width'), 'min_height' => config('medialibrary-extensions.min_image_height')]));

    // test that an image that is too large is rejected
    config(['medialibrary-extensions.max_image_width' => 15]);
    config(['medialibrary-extensions.max_image_height' => 15]);
    $page->attach($inputSelector, $this->getTinyImageFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.image_too_large', ['width' => 16, 'height' => 16, 'max_width' => config('medialibrary-extensions.max_image_width'), 'max_height' => config('medialibrary-extensions.max_image_height')]));

    // test that too large images (file size) are rejected
    config(['medialibrary-extensions.max_upload_size' => 1024]);
    $page->attach($inputSelector, $this->getRandomFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::validation.media_max', ['max' => mle_human_filesize(config('medialibrary-extensions.max_upload_size'))]));

    $page->page()->close();
})->group('browser')
    ->with('validation_matrix')
    ->flaky();

it('can upload YouTube video single', function ($theme, $dataSource, $xhr, $storage) {

    // prepare selectors
    $mediaManagerId = '#alien-single-'.$storage.'-mms';
    $inputSelector = $mediaManagerId.' [data-mle-youtube-input]';
    $uploadButtonSelector = $mediaManagerId.' [data-mle-youtube-upload-button]';
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
    $mediaModalCarouselItemContainerSelector = $mediaModalCarouselItemSelector.' .mle-media-carousel-item-container';
    $mediaModalCarouselItemContainerLiteYouTubeSelector = $mediaModalCarouselItemContainerSelector.' lite-youtube';

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    $dataSourceResolver = app(DataSourceResolver::class);
    $resolvedConnection = $dataSourceResolver->resolveConnection($dataSource);

    $this->assertDatabaseCount('media', 0, $resolvedConnection);
    $this->assertDatabaseCount('mle_temporary_uploads', 0, $resolvedConnection);

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $mediaManagerId);

    $page->assertPresent($inputSelector)

        // assert that the upload button is initially enabled
        ->assertButtonEnabled($uploadButtonSelector)

        // test that it shows an error when no YouTube url entered
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.please_wait'))
        ->assertSee(__('medialibrary-extensions::messages.upload_no_youtube_url'))

        // enter youtube url
        ->type($inputSelector, $this->getYouTubeFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.please_wait'))
        ->assertSee(__('medialibrary-extensions::messages.youtube_video_uploaded'))

        // assert that the image is visible in the preview
        ->assertPresent($gridSelector.' [data-mle-media-preview-item]:first-child')

        // assert that the upload button is disabled after upload (single media)
        ->assertButtonDisabled($uploadButtonSelector)

        // assert grid is present
        ->assertPresent($gridSelector)

        // assert grid has the media container
        ->assertPresent($firstMediaPreviewContainer)

        // check that the media item's menu has the expected buttons and state
        ->assertMissing($editButtonSelector)
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
        ->assertPresent($mediaModalCarouselItemContainerSelector)
        ->assertPresent($mediaModalCarouselItemContainerLiteYouTubeSelector)

        // check that media modal can be closed
        ->pressAndWaitFor($mediaModalCloseButtonSelector, $waitTime)

        // check delete media works
        ->pressAndWaitFor($deleteButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.please_wait'));

    if ($xhr) {
        $page->assertSee(__('medialibrary-extensions::messages.medium_removed'));
    }
    // the upload button should be enabled again
    $page->assertButtonEnabled($uploadButtonSelector);

    $page->page()->close();
})->group('browser')
    ->with('mms_youtube_test_matrix')
    ->flaky();
