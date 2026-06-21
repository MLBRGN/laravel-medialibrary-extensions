<?php

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
});

$waitTimeXhr = .1;
$waitTImeNonXhr = .3;// non-xhr tests are slower (0.3 seems the minimum for me)

dataset('mms_test_matrix', [
    'bootstrap + default + xhr + permanent' => ['bootstrap-5', 'default', true, 'permanent'],
//    'bootstrap + default + xhr + temporary' => ['bootstrap-5', 'default', true, 'temporary'],
    'bootstrap + default + no xhr + permanent' => ['bootstrap-5', 'default', false, 'permanent'],
//    'bootstrap + default + no xhr + temporary' => ['bootstrap-5', 'default', false, 'temporary'],

    'bootstrap + demo + xhr + permanent' => ['bootstrap-5', 'demo', true, 'permanent'],
//    'bootstrap + demo + xhr + temporary' => ['bootstrap-5', 'demo', true, 'temporary'],
    'bootstrap + demo + no xhr + permanent' => ['bootstrap-5', 'demo', false, 'permanent'],
//    'bootstrap + demo + no xhr + temporary' => ['bootstrap-5', 'demo', false, 'temporary'],

    'plain + default + xhr + permanent' => ['plain', 'default', true, 'permanent'],
//    'plain + default + xhr + temporary' => ['plain', 'default', true, 'temporary'],
    'plain + default + no xhr + permanent' => ['plain', 'default', false, 'permanent'],
//    'plain + default + no xhr + temporary' => ['plain', 'default', false, 'temporary'],

    'plain + demo + xhr + permanent' => ['plain', 'demo', true, 'permanent'],
//    'plain + demo + xhr + temporary' => ['plain', 'demo', true, 'temporary'],
    'plain + demo + no xhr + permanent' => ['plain', 'demo', false, 'permanent'],
//    'plain + demo + no xhr + temporary' => ['plain', 'demo', false, 'temporary'],
]);

dataset('mmm_test_matrix', [
    'bootstrap + default + xhr + permanent' => ['bootstrap-5', 'default', true, 'permanent'],
//    'bootstrap + default + xhr + temporary' => ['bootstrap-5', 'default', true, 'temporary'],
//    'bootstrap + default + no xhr + permanent' => ['bootstrap-5', 'default', false, 'permanent'],
//    'bootstrap + default + no xhr + temporary' => ['bootstrap-5', 'default', false, 'temporary'],

    'bootstrap + demo + xhr + permanent' => ['bootstrap-5', 'demo', true, 'permanent'],
//    'bootstrap + demo + xhr + temporary' => ['bootstrap-5', 'demo', true, 'temporary'],
//    'bootstrap + demo + no xhr + permanent' => ['bootstrap-5', 'demo', false, 'permanent'],
//    'bootstrap + demo + no xhr + temporary' => ['bootstrap-5', 'demo', false, 'temporary'],

    'plain + default + xhr + permanent' => ['plain', 'default', true, 'permanent'],
//    'plain + default + xhr + temporary' => ['plain', 'default', true, 'temporary'],
//    'plain + default + no xhr + permanent' => ['plain', 'default', false, 'permanent'],
//    'plain + default + no xhr + temporary' => ['plain', 'default', false, 'temporary'],

    'plain + demo + xhr + permanent' => ['plain', 'demo', true, 'permanent'],
//    'plain + demo + xhr + temporary' => ['plain', 'demo', true, 'temporary'],
//    'plain + demo + no xhr + permanent' => ['plain', 'demo', false, 'permanent'],
//    'plain + demo + no xhr + temporary' => ['plain', 'demo', false, 'temporary'],
]);

dataset('mms_youtube_test_matrix', [
    'bootstrap + default + xhr + permanent' => ['bootstrap-5', 'default', true, 'permanent'],
//    'bootstrap + default + xhr + temporary' => ['bootstrap-5', 'default', true, 'temporary'],
    'bootstrap + default + no xhr + permanent' => ['bootstrap-5', 'default', false, 'permanent'],
//    'bootstrap + default + no xhr + temporary' => ['bootstrap-5', 'default', false, 'temporary'],

    'bootstrap + demo + xhr + permanent' => ['bootstrap-5', 'demo', true, 'permanent'],
//    'bootstrap + demo + xhr + temporary' => ['bootstrap-5', 'demo', true, 'temporary'],
    'bootstrap + demo + no xhr + permanent' => ['bootstrap-5', 'demo', false, 'permanent'],
//    'bootstrap + demo + no xhr + temporary' => ['bootstrap-5', 'demo', false, 'temporary'],

    // TODO plain tests sometimes fail?
    'plain + default + xhr + permanent' => ['plain', 'default', true, 'permanent'],
//    'plain + default + xhr + temporary' => ['plain', 'default', true, 'temporary'],
    'plain + default + no xhr + permanent' => ['plain', 'default', false, 'permanent'],
//    'plain + default + no xhr + temporary' => ['plain', 'default', false, 'temporary'],

    'plain + demo + xhr + permanent' => ['plain', 'demo', true, 'permanent'],
//    'plain + demo + xhr + temporary' => ['plain', 'demo', true, 'temporary'],
    'plain + demo + no xhr + permanent' => ['plain', 'demo', false, 'permanent'],
//    'plain + demo + no xhr + temporary' => ['plain', 'demo', false, 'temporary'],
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
//})->group('browser')
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
});
//})
//    ->skip();

it('can control mms', function ($theme, $dataSource, $xhr, $storage) use ($waitTimeXhr, $waitTImeNonXhr) {

    // prepare selectors
    $mediaManagerId = '#alien-single-'.$storage.'-mms';
    $inputSelector = $mediaManagerId . ' [data-mle-media-input]';
    $uploadButtonSelector = $mediaManagerId . ' [data-mle-media-upload-button]';
    $gridSelector = $mediaManagerId . ' [data-mle-media-preview-grid]';
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

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $waitTimeXhr : $waitTImeNonXhr;

    $page = $this->visit("/mle-demo?theme={$theme}&data_source={$dataSource}&use_xhr={$xhrInt}#{$mediaManagerId}")
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
        ->assertButtonDisabled($uploadButtonSelector)

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
//    ->pressAndWaitFor($editButtonSelector, $waitTime)
//    ->assertPresent($imageEditorModalSelector)
//    ->pressAndWaitFor($imageEditorModalSaveButtonSelector, $waitTime)

//     TODO not available in mms
//    ->pressAndWaitFor($setAsFirstButtonSelector, $waitTime)
//    ->waitForText(__('medialibrary-extensions::messages.please_wait'))
//    ->waitForText(__('medialibrary-extensions::messages.medium_set_as_main'))

    // check delete media works
    ->pressAndWaitFor($deleteButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.medium_removed'))

    // the upload button should be enabled again
    ->assertButtonEnabled($uploadButtonSelector);

    //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

})->group('browser')
    ->with('mms_test_matrix');
//    ->with('mms_test_matrix')
//    ->skip();
// ->only();

it('can control mmm', function ($theme, $dataSource, $xhr, $storage) use ($waitTimeXhr, $waitTImeNonXhr) {

    Config::set('medialibrary-extensions.max_items_in_shared_media_collections', 3);

    // prepare selectors
    $mediaManagerId = '#alien-multiple-'.$storage.'-mmm';
    $inputSelector = $mediaManagerId . ' [data-mle-media-input]';
    $uploadButtonSelector = $mediaManagerId . ' [data-mle-media-upload-button]';
    $gridSelector = $mediaManagerId . ' [data-mle-media-preview-grid]';
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

    $page = $this->visit("/mle-demo?theme={$theme}&data_source={$dataSource}&use_xhr={$xhrInt}#{$mediaManagerId}")
        ->assertNoJavaScriptErrors()

        ->assertPresent($inputSelector)

        // assert that upload button is initially enabled
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

        // assert grid has media container
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

            Log::info('Deleting media item ' . $i);
            // check delete media works
//            $page
//                ->assertPresent($deleteButtonSelector)
//                ->pressAndWaitFor($deleteButtonSelector, $waitTime);
            $page->pressAndWaitFor($deleteButtonSelector, $waitTime)
                ->waitForText(__('medialibrary-extensions::messages.please_wait'))
                ->waitForText(__('medialibrary-extensions::messages.medium_removed'));

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
    $inputSelector = $mediaManagerId . ' [data-mle-youtube-input]';
    $uploadButtonSelector = $mediaManagerId . ' [data-mle-youtube-upload-button]';
    $gridSelector = $mediaManagerId . ' [data-mle-media-preview-grid]';
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

    $page = $this->visit("/mle-demo?theme={$theme}&data_source={$dataSource}&use_xhr={$xhrInt}#{$mediaManagerId}")
        ->assertNoJavaScriptErrors()

        ->assertPresent($inputSelector)

        // assert that the upload button is initially enabled
        ->assertButtonEnabled($uploadButtonSelector)

        // test that it shows error when no YouTube url entered
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

        // assert that upload button is disabled after upload (single media)
        ->assertButtonDisabled($uploadButtonSelector)

        // assert that the image is visible in the preview
        //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

        // assert grid is present
        ->assertPresent($gridSelector)

        // assert grid has media container
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
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.medium_removed'))

        // the upload button should be enabled again
        ->assertButtonEnabled($uploadButtonSelector);

    //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

})->group('browser')
    ->with('mms_youtube_test_matrix');
//    ->with('mms_youtube_test_matrix')
//    ->only();
