<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\View\AnonymousComponent;

beforeEach(function () {
    // TODO fix: why do i need to do this?
    // Mock laravel-form-components
    Blade::component('form-form', AnonymousComponent::class);
    Blade::component('form-html-editor', AnonymousComponent::class);
    Blade::component('form-input', AnonymousComponent::class);
    Blade::component('form-checkbox', AnonymousComponent::class);
    Blade::component('form-select', AnonymousComponent::class);
    Blade::component('form-submit', AnonymousComponent::class);

    config(['medialibrary-extensions.demo_pages_enabled' => true]);

});

$waitTimeXhr = .1;
$waitTImeNonXhr = .3; // non-xhr tests are slower (0.3 seems the minimum for me)

dataset('mms_test_matrix', [
    'bootstrap + default + xhr + permanent' => ['bootstrap-5', 'default', true, 'permanent'],
    'bootstrap + default + xhr + temporary' => ['bootstrap-5', 'default', true, 'temporary'],
    'bootstrap + default + no xhr + permanent' => ['bootstrap-5', 'default', false, 'permanent'],
    'bootstrap + default + no xhr + temporary' => ['bootstrap-5', 'default', false, 'temporary'],

    'bootstrap + demo + xhr + permanent' => ['bootstrap-5', 'demo', true, 'permanent'],
    'bootstrap + demo + xhr + temporary' => ['bootstrap-5', 'demo', true, 'temporary'],
    'bootstrap + demo + no xhr + permanent' => ['bootstrap-5', 'demo', false, 'permanent'],
    'bootstrap + demo + no xhr + temporary' => ['bootstrap-5', 'demo', false, 'temporary'],

    'plain + default + xhr + permanent' => ['plain', 'default', true, 'permanent'],
    'plain + default + xhr + temporary' => ['plain', 'default', true, 'temporary'],
    'plain + default + no xhr + permanent' => ['plain', 'default', false, 'permanent'],
    'plain + default + no xhr + temporary' => ['plain', 'default', false, 'temporary'],

    'plain + demo + xhr + permanent' => ['plain', 'demo', true, 'permanent'],
    'plain + demo + xhr + temporary' => ['plain', 'demo', true, 'temporary'],
    'plain + demo + no xhr + permanent' => ['plain', 'demo', false, 'permanent'],
    'plain + demo + no xhr + temporary' => ['plain', 'demo', false, 'temporary'],
]);

dataset('mmm_test_matrix', [
    'bootstrap + default + xhr + permanent' => ['bootstrap-5', 'default', true, 'permanent'],
    'bootstrap + default + xhr + temporary' => ['bootstrap-5', 'default', true, 'temporary'],
    'bootstrap + default + no xhr + permanent' => ['bootstrap-5', 'default', false, 'permanent'],
    'bootstrap + default + no xhr + temporary' => ['bootstrap-5', 'default', false, 'temporary'],

    'bootstrap + demo + xhr + permanent' => ['bootstrap-5', 'demo', true, 'permanent'],
    'bootstrap + demo + xhr + temporary' => ['bootstrap-5', 'demo', true, 'temporary'],
    'bootstrap + demo + no xhr + permanent' => ['bootstrap-5', 'demo', false, 'permanent'],
    'bootstrap + demo + no xhr + temporary' => ['bootstrap-5', 'demo', false, 'temporary'],

    'plain + default + xhr + permanent' => ['plain', 'default', true, 'permanent'],
    'plain + default + xhr + temporary' => ['plain', 'default', true, 'temporary'],
    'plain + default + no xhr + permanent' => ['plain', 'default', false, 'permanent'],
    'plain + default + no xhr + temporary' => ['plain', 'default', false, 'temporary'],

    'plain + demo + xhr + permanent' => ['plain', 'demo', true, 'permanent'],
    'plain + demo + xhr + temporary' => ['plain', 'demo', true, 'temporary'],
    'plain + demo + no xhr + permanent' => ['plain', 'demo', false, 'permanent'],
    'plain + demo + no xhr + temporary' => ['plain', 'demo', false, 'temporary'],
]);

dataset('mms_youtube_test_matrix', [
    'bootstrap + default + xhr + permanent' => ['bootstrap-5', 'default', true, 'permanent'],
    'bootstrap + default + xhr + temporary' => ['bootstrap-5', 'default', true, 'temporary'],
    'bootstrap + default + no xhr + permanent' => ['bootstrap-5', 'default', false, 'permanent'],// TODO fails
    'bootstrap + default + no xhr + temporary' => ['bootstrap-5', 'default', false, 'temporary'],

    'bootstrap + demo + xhr + permanent' => ['bootstrap-5', 'demo', true, 'permanent'],
    'bootstrap + demo + xhr + temporary' => ['bootstrap-5', 'demo', true, 'temporary'],
    'bootstrap + demo + no xhr + permanent' => ['bootstrap-5', 'demo', false, 'permanent'],
    'bootstrap + demo + no xhr + temporary' => ['bootstrap-5', 'demo', false, 'temporary'],

    'plain + default + xhr + permanent' => ['plain', 'default', true, 'permanent'],
    'plain + default + xhr + temporary' => ['plain', 'default', true, 'temporary'],
    'plain + default + no xhr + permanent' => ['plain', 'default', false, 'permanent'],
    'plain + default + no xhr + temporary' => ['plain', 'default', false, 'temporary'],

    'plain + demo + xhr + permanent' => ['plain', 'demo', true, 'permanent'],
    'plain + demo + xhr + temporary' => ['plain', 'demo', true, 'temporary'],
    'plain + demo + no xhr + permanent' => ['plain', 'demo', false, 'permanent'],
    'plain + demo + no xhr + temporary' => ['plain', 'demo', false, 'temporary'],
]);

it('loads all required assets', function () {

    $this->visit('/mle-demo')
        ->assertNoJavaScriptErrors();

    $assetPath = config('medialibrary-extensions.asset_path');

    // Verify core JS
    $this->get($assetPath.'/js/core/media-library-loader.js')
        ->assertSuccessful();

    // Verify theme-specific assets
    $this->get($assetPath.'/css/bootstrap-5.css')
        ->assertSuccessful();
    $this->get($assetPath.'/js/bootstrap-5.js')
        ->assertSuccessful();

    // Verify image editor
    $this->get($assetPath.'/js/image-editor.js')
        ->assertSuccessful();
})->group('browser');
// })->group('browser')
//    ->skip();

it('can visit demo page switch theme, XHR and DataSource', function () {

    $page = $this->visit('/mle-demo?theme=bootstrap-5&data_source=default&use_xhr=0')
        ->assertNoJavaScriptErrors()

        // Theme switching
        ->click('@btn-theme-plain')
        ->assertQueryStringHas('theme', 'plain')

        ->click('@btn-theme-bootstrap-5')
        ->assertQueryStringHas('theme', 'bootstrap-5')

        // DataSource switching
        ->click('@btn-data-source-default')
        ->assertQueryStringHas('data_source', 'default')

        ->click('@btn-data-source-demo')
        ->assertQueryStringHas('data_source', 'demo')

        // XHR mode switching
        ->click('@btn-use-xhr-no')
        ->assertQueryStringHas('use_xhr', '0')

        ->click('@btn-use-xhr-yes')
        ->assertQueryStringHas('use_xhr', '1')

        ->assertSee('Laravel Media Library Extensions Component tests')
        ->assertSee('Media Manager Single')
        ->assertSee('Media Manager Multiple')
        ->assertSee('Media Carousel')
        ->assertSee('Media Lab')
        ->assertSee('Media First Available');
})
    ->group('browser');
// })
//    ->skip();

it('can control mms', function ($theme, $dataSource, $xhr, $storage) use ($waitTimeXhr, $waitTImeNonXhr) {

    // prepare selectors
    $mediaManagerId = '#alien-single-'.$storage.'-mms';
    $inputSelector = $mediaManagerId.' [data-mle-media-input]';
    $uploadButtonSelector = $mediaManagerId.' [data-mle-media-upload-button]';
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
    $waitTime = $xhr ? $waitTimeXhr : $waitTImeNonXhr;

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt#$mediaManagerId")
//        ->setViewportSize(1900, 1000)

        ->assertNoJavaScriptErrors()

        ->assertPresent($inputSelector)

        // assert that the upload button is initially enabled
        ->assertButtonEnabled($uploadButtonSelector)

        // test that it shows error when no file selected
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.upload_no_files'))

        // test that invalid mime types are rejected
        ->attach($inputSelector, $this->getInvalidMimeTypeFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.upload_failed_due_to_invalid_mimetype'))

        // attach an image file and submit and check if spinner shows and upload is successful
        ->attach($inputSelector, $this->getRandomFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.upload_success'))

        // assert that the image is visible in the preview
        ->assertPresent($gridSelector.' [data-mle-media-preview-item]:first-child')

        // assert that the upload button is disabled after upload (single media)
//        ->assertButtonDisabled($uploadButtonSelector)

    // assert that the image is visible in the preview
    //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

    // assert grid is present
        ->assertPresent($gridSelector)

    // assert grid has the media container
        ->assertPresent($firstMediaPreviewContainer)

    // check that the media item's menu has the expected buttons and state
        ->assertButtonEnabled($editButtonSelector)
        ->assertButtonDisabled($setAsFirstButtonSelector)
        ->assertButtonEnabled($deleteButtonSelector)

    // check media modal opening and presence of expected elements
        ->assertPresent($mediaPreviewImageSelector)
        ->pressAndWaitFor($mediaPreviewImageSelector, $waitTime)
//    ->assertVisible(mediaModalSelector)
        ->assertPresent($mediaModalSelector)
        ->assertPresent($mediaModalCloseButtonSelector)
        ->assertPresent($mediaModalCarouselSelector)
        ->assertPresent($mediaModalCarouselIndicatorSelector)
        ->assertPresent($mediaModalCarouselItemSelector)
        ->assertPresent($mediaModalCarouselItemContainerSelector)
        ->assertPresent($mediaModalCarouselItemContainerImageSelector)

    // check that media modal can be closed
        ->pressAndWaitFor($mediaModalCloseButtonSelector, $waitTime)

    // check image editor modal can be opened and closed
        ->pressAndWaitFor($editButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSelector)
        ->pressAndWaitFor($imageEditorModalCloseButtonSelector, $waitTime)

    // check saving image in the image editor
        ->pressAndWaitFor($editButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSelector)
        ->pressAndWaitFor($imageEditorModalRotateCcwButtonSelector, $waitTime)
        ->pressAndWaitFor($imageEditorModalSaveButtonSelector, $waitTime)

//     TODO not available in mms
//    ->pressAndWaitFor($setAsFirstButtonSelector, $waitTime)
//    ->waitForText(__('medialibrary-extensions::messages.please_wait'))
//    ->waitForText(__('medialibrary-extensions::messages.medium_set_as_main'))

    // check delete media works
        ->pressAndWaitFor($deleteButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'));

    // TODO non-xhr does not show delete message
    if ($xhr) {
        $page->waitForText(__('medialibrary-extensions::messages.medium_removed'));
    }

    // the upload button should be enabled again
        $page->assertButtonEnabled($uploadButtonSelector);

    //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

})->group('browser')
    ->with('mms_test_matrix')
//    ->with('mms_test_matrix')
    ->skip();
// ->only();

it('can control mms 2', function ($theme, $dataSource, $xhr, $storage) use ($waitTimeXhr, $waitTImeNonXhr) {

    // prepare selectors
    $mediaManagerId = '#alien-single-'.$storage.'-mms';
    $inputSelector = $mediaManagerId.' [data-mle-media-input]';
    $uploadButtonSelector = $mediaManagerId.' [data-mle-media-upload-button]';
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
    $waitTime = $xhr ? $waitTimeXhr : $waitTImeNonXhr;

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt#$mediaManagerId")
        ->assertNoJavaScriptErrors()

        ->assertPresent($inputSelector)

        // assert that the upload button is initially enabled
        ->assertButtonEnabled($uploadButtonSelector)

        // test that it shows error when no file selected
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.upload_no_files'))

        // test that invalid mime types are rejected
        ->attach($inputSelector, $this->getInvalidMimeTypeFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.upload_failed_due_to_invalid_mimetype'))

        // attach an image file and submit and check if spinner shows and upload is successful
        ->attach($inputSelector, $this->getRandomFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.upload_success'))

        // assert that the image is visible in the preview
        ->assertPresent($gridSelector.' [data-mle-media-preview-item]:first-child')

        // assert that the upload button is disabled after upload (single media)
// FIXME
//        ->assertButtonDisabled($uploadButtonSelector)

        // assert that the image is visible in the preview
        //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

        // assert grid is present
        ->assertPresent($gridSelector)

        // assert grid has the media container
        ->assertPresent($firstMediaPreviewContainer)

        // check that the media item's menu has the expected buttons and state
        ->assertButtonEnabled($editButtonSelector)
//     TODO not available in mms, should not be visible at all
//        ->assertMissing($setAsFirstButtonSelector)
        ->assertButtonDisabled($setAsFirstButtonSelector)
        ->assertButtonEnabled($deleteButtonSelector)

        // check media modal opening and presence of expected elements
        ->assertPresent($mediaPreviewImageSelector)
        ->pressAndWaitFor($mediaPreviewImageSelector, $waitTime)
//    ->assertVisible(mediaModalSelector)
        ->assertPresent($mediaModalSelector)
        ->assertPresent($mediaModalCloseButtonSelector)
        ->assertPresent($mediaModalCarouselSelector)
        ->assertPresent($mediaModalCarouselIndicatorSelector)
        ->assertPresent($mediaModalCarouselItemSelector)
        ->assertPresent($mediaModalCarouselItemContainerSelector)
        ->assertPresent($mediaModalCarouselItemContainerImageSelector)

        // check that media modal can be closed
        ->pressAndWaitFor($mediaModalCloseButtonSelector, $waitTime)

        // check image editor modal can be opened and closed
        ->pressAndWaitFor($editButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSelector)
        ->assertVisible($imageEditorModalSelector)
        ->assertDontSee(__('medialibrary-extensions::messages.could_not_initialize_image_editor'))
        ->pressAndWaitFor($imageEditorModalCloseButtonSelector, $waitTime)
        ->assertMissing($imageEditorModalSelector)

        // check saving edited image in the image editor
        ->pressAndWaitFor($editButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSelector)
        ->assertVisible($imageEditorModalSelector)
        ->assertDontSee(__('medialibrary-extensions::messages.could_not_initialize_image_editor'))
        ->pressAndWaitFor($imageEditorModalRotateCcwButtonSelector, $waitTime)
        ->pressAndWaitFor($imageEditorModalSaveButtonSelector, $waitTime)
        ->assertMissing($imageEditorModalSelector)
// TODO
//        ->waitForText(__('medialibrary-extensions::messages.medium_replaced'))

//     TODO not available in mms
//    ->pressAndWaitFor($setAsFirstButtonSelector, $waitTime)
//    ->waitForText(__('medialibrary-extensions::messages.please_wait'))
//    ->waitForText(__('medialibrary-extensions::messages.medium_set_as_main'))

        // check canceling image editing in the image editor
// TODO image editor modal was not closed after canceling
//    ->pressAndWaitFor($editButtonSelector, $waitTime)
//    ->assertVisible($imageEditorModalSelector)
//    ->assertDontSee(__('medialibrary-extensions::messages.could_not_initialize_image_editor'))
//    ->pressAndWaitFor($imageEditorModalCancelButtonSelector, $waitTime)
//    ->assertMissing($imageEditorModalSelector)

        // check delete media works
        ->pressAndWaitFor($deleteButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'));

       // TODO non-xhr does not show delete message
        if ($xhr) {
            $page->waitForText(__('medialibrary-extensions::messages.medium_removed'));
        }

        // the upload button should be enabled again
        $page->assertButtonEnabled($uploadButtonSelector);

    //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

})->group('browser')
//    ->with('mms_test_matrix');
    ->with('mms_test_matrix')
    ->skip();
// ->only();

it('can control mmm', function ($theme, $dataSource, $xhr, $storage) use ($waitTimeXhr, $waitTImeNonXhr) {

    Config::set('medialibrary-extensions.max_items_in_shared_media_collections', 3);

    // prepare selectors
    $mediaManagerId = '#alien-multiple-'.$storage.'-mmm';
    $inputSelector = $mediaManagerId.' [data-mle-media-input]';
    $uploadButtonSelector = $mediaManagerId.' [data-mle-media-upload-button]';
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

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $waitTimeXhr : $waitTImeNonXhr;

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt#$mediaManagerId")
        ->assertNoJavaScriptErrors()

        ->assertPresent($inputSelector)

        // assert that the upload button is initially enabled
        ->assertButtonEnabled($uploadButtonSelector);

    // test that it shows error when no file selected
    $page->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.upload_no_files'));

    // test that invalid mime types are rejected
    //    $page->attach($inputSelector, $this->getInvalidMimeTypeFixture())
    //        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
    //        ->waitForText(__('medialibrary-extensions::messages.upload_failed_due_to_invalid_mimetype'));

    $maxItems = config('medialibrary-extensions.max_items_in_shared_media_collections');
    for ($i = 0; $i < $maxItems; $i++) {
        // attach an image file and submit and check if spinner shows and upload is successful
        $page->attach($inputSelector, $this->getRandomFixture())
            ->pressAndWaitFor($uploadButtonSelector, $waitTime)
            ->waitForText(__('medialibrary-extensions::messages.please_wait'))
            ->waitForText(__('medialibrary-extensions::messages.upload_success'));
    }

    // assert that the image is visible in the preview
    $page->assertPresent($gridSelector.' [data-mle-media-preview-item]:first-child')

    // TODO fix: assert that upload button is disabled after uploading maxItems
//        ->assertButtonDisabled($uploadButtonSelector)

    // assert that the image is visible in the preview
    //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

    // assert grid is present
        ->assertPresent($gridSelector)

    // assert grid has the media container
        ->assertPresent($firstMediaPreviewContainer)

    // check that the media item's menu has the expected buttons and state
        ->assertButtonEnabled($editButtonSelector)
        ->assertButtonDisabled($setAsFirstButtonSelector)
        ->assertButtonEnabled($deleteButtonSelector)

    // check media modal opening and presence of expected elements
        ->assertPresent($mediaPreviewImageSelector)
        ->pressAndWaitFor($mediaPreviewImageSelector, $waitTime)
//    ->assertVisible(mediaModalSelector)
        ->assertPresent($mediaModalSelector)
        ->assertPresent($mediaModalCloseButtonSelector)
        ->assertPresent($mediaModalCarouselSelector)
        ->assertPresent($mediaModalCarouselIndicatorSelector)
        ->assertPresent($mediaModalCarouselItemSelector)

    // check that media modal can be closed
        ->pressAndWaitFor($mediaModalCloseButtonSelector, $waitTime)

    // check image editor modal can be opened and closed
        ->pressAndWaitFor($editButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSelector)
        ->pressAndWaitFor($imageEditorModalCloseButtonSelector, $waitTime);

    // delete media test
    for ($i = 0; $i < $maxItems; $i++) {

        Log::info('Deleting media item '.$i);
        // check delete media works
        //            $page
        //                ->assertPresent($deleteButtonSelector)
        //                ->pressAndWaitFor($deleteButtonSelector, $waitTime);
        $page->pressAndWaitFor($deleteButtonSelector, $waitTime)
            ->waitForText(__('medialibrary-extensions::messages.please_wait'));

        if($xhr) {
            $page->waitForText(__('medialibrary-extensions::messages.medium_removed'));
        }

    }

    // the upload button should be enabled again
    $page->assertButtonEnabled($uploadButtonSelector);

    //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

})->group('browser')
    ->with('mmm_test_matrix');
//    ->with('mmm_test_matrix')
// ->only();
//    ->skip();

// TODO: more complex, youtube downloading does not work in tests, need to stub?
it('can upload YouTube video single', function ($theme, $dataSource, $xhr, $storage) use ($waitTimeXhr, $waitTImeNonXhr) {

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
    $waitTime = $xhr ? $waitTimeXhr : $waitTImeNonXhr;

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt#$mediaManagerId")
        ->assertNoJavaScriptErrors()

        ->assertPresent($inputSelector)

        // assert that the upload button is initially enabled
        ->assertButtonEnabled($uploadButtonSelector)

        // test that it shows an error when no YouTube url entered
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.upload_no_youtube_url'))

        // enter youtube url
        ->type($inputSelector, $this->getYouTubeFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.youtube_video_uploaded'))
//        ->waitForText(__('medialibrary-extensions::messages.youtube_thumbnail_download_failed'))

        // assert that the image is visible in the preview
        ->assertPresent($gridSelector.' [data-mle-media-preview-item]:first-child')

        // assert that the upload button is disabled after upload (single media)
//        ->assertButtonDisabled($uploadButtonSelector)

        // assert that the image is visible in the preview
        //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

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
//    ->assertVisible(mediaModalSelector)
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
        ->waitForText(__('medialibrary-extensions::messages.please_wait'));

    // TODO non-xhr does not show delete message
        if ($xhr) {
            $page->waitForText(__('medialibrary-extensions::messages.medium_removed'));
        }
        // the upload button should be enabled again
        $page->assertButtonEnabled($uploadButtonSelector);

    //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

})->group('browser')
    ->with('mms_youtube_test_matrix');
//->only();

it('can control standalone media carousel', function ($theme, $dataSource, $xhr, $uploadMedia = false) use ($waitTimeXhr, $waitTImeNonXhr) {

    // prepare MMM selectors to upload media first
    $mmmId = '#alien-multiple-permanent-mmm';
    $mmmInputSelector = $mmmId.' [data-mle-media-input]';
    $mmmUploadButtonSelector = $mmmId.' [data-mle-media-upload-button]';

    // prepare carousel selectors
    $carouselId = '#alien-carousel-crs';
    $indicatorsSelector = $carouselId.' [data-mle-carousel-indicators]';
    $nextButtonSelector = $carouselId.' [data-mle-carousel-next]';
    $prevButtonSelector = $carouselId.' [data-mle-carousel-prev]';
    $firstItemSelector = $carouselId.' [data-mle-carousel-item]:first-child';
    $secondItemSelector = $carouselId.' [data-mle-carousel-item]:nth-child(2)';

    // modal selectors
    $modalId = '#alien-carousel-mod';
    $modalSelector = $modalId.'[data-mle-media-modal]';
    $modalCloseButtonSelector = $modalSelector.' [data-mle-modal-close]';

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $waitTimeXhr : $waitTImeNonXhr;
    $scrollToId = 'alien-carousel-crs';

    // TODO scrolling not working scroll id is removed from url?
    //    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt#$scrollToId")
    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt#alien-carousel-crs")
        ->assertNoJavaScriptErrors();

    // don't upload each iteration
    if ($uploadMedia) {
        // 1. Upload two images via MMM
        $page->attach($mmmInputSelector, $this->getRandomFixture())
            ->pressAndWaitFor($mmmUploadButtonSelector, $waitTime)
            ->waitForText(__('medialibrary-extensions::messages.upload_success'));

        $page->attach($mmmInputSelector, $this->getRandomFixture())
            ->pressAndWaitFor($mmmUploadButtonSelector, $waitTime)
            ->waitForText(__('medialibrary-extensions::messages.upload_success'));
    }

    // 2. Refresh the page to see them in Carousel
    $page->refresh()
        ->assertPresent($carouselId)
        ->assertPresent($carouselId)
        ->assertPresent($indicatorsSelector)
        ->assertPresent($nextButtonSelector)
        ->assertPresent($prevButtonSelector)
        ->assertPresent($firstItemSelector)
        ->assertAttributeContains($firstItemSelector, 'class', 'active')

        // click next
        ->click($nextButtonSelector)
        ->wait(0.5) // Wait for transition
        ->assertAttributeContains($secondItemSelector, 'class', 'active')
//        ->assertAttributeMissing($firstItemSelector, 'class', 'active')

        // click prev
        ->click($prevButtonSelector)
        ->wait(0.5) // Wait for transition
        ->assertAttributeContains($firstItemSelector, 'class', 'active')
//        ->assertAttributeMissing($secondItemSelector, 'class', 'active')

        // click the indicator for the second item
        ->click($indicatorsSelector.' [data-mle-slide-to="1"]')
        ->wait(0.5) // Wait for transition
        ->assertAttributeContains($secondItemSelector, 'class', 'active')

        // test modal expansion if applicable (default is true)
//        ->click($secondItemSelector.' [data-mle-modal-trigger]')
        ->click($secondItemSelector)
        ->wait(0.5)
        ->assertPresent($modalSelector)
        ->click($modalCloseButtonSelector)
        ->wait(0.5)
        ->assertMissing($modalSelector); // not visible

})->group('browser')
    ->with([
        'bootstrap + default + xhr' => ['bootstrap-5', 'default', true, true],
        'bootstrap + demo + no xhr' => ['bootstrap-5', 'demo', false, true],
        'plain + default + xhr' => ['plain', 'default', true],
        'plain + demo + no xhr' => ['plain', 'demo', false],
    ]);

it('can control media lab', function ($theme, $dataSource, $xhr) use ($waitTimeXhr, $waitTImeNonXhr) {

    // prepare MMM selectors to upload media first
    $mmmId = '#alien-multiple-permanent-mmm';
    $mmmInputSelector = $mmmId.' [data-mle-media-input]';
    $mmmUploadButtonSelector = $mmmId.' [data-mle-media-upload-button]';

    // prepare media lab selectors
    $labId = '#alien-lab-lab';
    //    $labSelector = $labId.'[data-mle-media-manager-lab]';
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
    $waitTime = $xhr ? $waitTimeXhr : $waitTImeNonXhr;

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt#alien-multiple-permanent-mmm")
        ->assertNoJavaScriptErrors()

        ->assertPresent($labId)
        ->assertPresent($labOriginalSelector)
        ->assertPresent($labBaseSelector)
        ->assertPresent($mmsSelector)

        // 3. Test image editor via nested MMS in Lab
        ->pressAndWaitFor($mmsEditButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSelector)
        ->pressAndWaitFor($imageEditorModalRotateCcwButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSaveButtonSelector)
        ->pressAndWaitFor($imageEditorModalSaveButtonSelector, $waitTime)

//        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->assertMissing($imageEditorModalSelector)

        // 4. Test restore original (only if not temporary, and the demo page uses permanent here)
        ->assertPresent($restoreButtonSelector)
        ->pressAndWaitFor($restoreButtonSelector, $waitTime)

        ->waitForText(__('medialibrary-extensions::messages.please_wait'));
    // TODO fix
//        ->waitForText(__('medialibrary-extensions::messages.restored_original'));

})->group('browser')
    ->with([
        'bootstrap + default + xhr' => ['bootstrap-5', 'default', true],
        'plain + default + xhr' => ['plain', 'default', true],
    ]);
