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

$waitTime = 0;

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
//    'bootstrap + default + no xhr + permanent' => ['bootstrap-5', 'default', false, 'permanent'], // TODO FAILS?
    'bootstrap + default + no xhr + temporary' => ['bootstrap-5', 'default', false, 'temporary'],

    'bootstrap + demo + xhr + permanent' => ['bootstrap-5', 'demo', true, 'permanent'],
    'bootstrap + demo + xhr + temporary' => ['bootstrap-5', 'demo', true, 'temporary'],
//    'bootstrap + demo + no xhr + permanent' => ['bootstrap-5', 'demo', false, 'permanent'],// TODO FAILS?
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
})->group('browser')->skip();

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
})->skip();

it('can control mms', function ($theme, $dataSource, $xhr, $storage) use ($waitTime) {

    // get the file input and the upload button (submit button)

    $mediaManager = '#alien-single-'.$storage.'-mms';
    $inputSelector = $mediaManager . ' [data-mle-media-input]';
    $uploadButtonSelector = $mediaManager . ' [data-mle-media-upload-button]';
    $gridSelector = $mediaManager . ' [data-mle-media-preview-grid]';
    $firstMediaPreviewContainer = $gridSelector.' [data-mle-media-preview-container]:first-child';
    $editButtonSelector = $firstMediaPreviewContainer.' [data-mle-media-edit-button]';
    $setAsFirstButtonSelector = $firstMediaPreviewContainer.' [data-mle-media-set-as-first-button]';
    $deleteButtonSelector = $firstMediaPreviewContainer.' [data-mle-media-delete-button]';

    // for modal testing
    $mediaPreviewItemSelector = $firstMediaPreviewContainer.' [data-test="media-preview-item"]';
    $mediaPreviewImageSelector = $mediaPreviewItemSelector.' [data-test="media-preview-image"]';
    $mediaModalSelector = $firstMediaPreviewContainer.' [data-test="media-modal"]';
    $mediaModalCloseButtonSelector = $mediaModalSelector.' [data-test="media-modal-close-button"]';

    // for modal carousel testing
    $mediaModalCarouselSelector = $mediaModalSelector.' [data-mle-carousel]';
    $mediaModalCarouselIndicatorSelector = $mediaModalCarouselSelector.' [data-mle-carousel-indicators]';
    $mediaModalCarouselItemSelector = $mediaModalCarouselSelector.' [data-mle-carousel-item]';

    $xhrInt = $xhr ? 1 : 0;
    $page = $this->visit("/mle-demo?theme={$theme}&data_source={$dataSource}&use_xhr={$xhrInt}")
        ->assertNoJavaScriptErrors()

        ->assertPresent($inputSelector)

        // assert that upload button is initially enabled
        ->assertButtonEnabled($uploadButtonSelector)

        // test that it shows error when no file selected
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.upload_no_files'))

        // test that invalid mime types are rejected
        ->attach($inputSelector, $this->getInvalidMimeTypeFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.upload_failed_due_to_invalid_mimetype'))

        // attach image file and submit and check if spinner shows and upload is successful
        ->attach($inputSelector, $this->getRandomFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.upload_success'))

        // assert that the image is visible in the preview
        ->assertPresent($gridSelector.' [data-test="media-preview-item"]:first-child')

        // assert that upload button is disabled after upload (single media)
        // TODO fails in some tests
        ->assertButtonDisabled($uploadButtonSelector)

    // assert that the image is visible in the preview
    //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

    // assert grid is present
    ->assertPresent($gridSelector)

    // assert grid has media container
    ->assertPresent($firstMediaPreviewContainer)

    // check that the media item's menu has the expected buttons and state
    ->assertButtonEnabled($editButtonSelector)
    // TODO fix fails in plain theme
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

    // check delete media works
    ->pressAndWaitFor($deleteButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.medium_removed'))

    // the upload button should be enabled again
    ->assertButtonEnabled($uploadButtonSelector);

    //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

})->group('browser')
    ->with('mms_test_matrix')
    ->skip();

it('can control mmm', function ($theme, $dataSource, $xhr, $storage) use ($waitTime) {

    Config::set('medialibrary-extensions.max_items_in_shared_media_collections', 3);

    // get the file input and the upload button (submit button)
    $mediaManager = '#alien-multiple-'.$storage.'-mmm';
    $inputSelector = $mediaManager . ' [data-mle-media-input]';
    $uploadButtonSelector = $mediaManager . ' [data-mle-media-upload-button]';
    $gridSelector = $mediaManager . ' [data-mle-media-preview-grid]';
    $firstMediaPreviewContainer = $gridSelector.' [data-mle-media-preview-container]:first-child';
    $editButtonSelector = $firstMediaPreviewContainer.' [data-mle-media-edit-button]';
    $setAsFirstButtonSelector = $firstMediaPreviewContainer.' [data-mle-media-set-as-first-button]';
    $deleteButtonSelector = $firstMediaPreviewContainer.' [data-mle-media-delete-button]';

    // for modal testing
    $mediaPreviewItemSelector = $firstMediaPreviewContainer.' [data-test="media-preview-item"]';
    $mediaPreviewImageSelector = $mediaPreviewItemSelector.' [data-test="media-preview-image"]';
    $mediaModalSelector = $firstMediaPreviewContainer.' [data-test="media-modal"]';
    $mediaModalCloseButtonSelector = $mediaModalSelector.' [data-test="media-modal-close-button"]';

    // for modal carousel testing
    $mediaModalCarouselSelector = $mediaModalSelector.' [data-mle-carousel]';
    $mediaModalCarouselIndicatorSelector = $mediaModalCarouselSelector.' [data-mle-carousel-indicators]';
    $mediaModalCarouselItemSelector = $mediaModalCarouselSelector.' [data-mle-carousel-item]';

    $xhrInt = $xhr ? 1 : 0;
    $page = $this->visit("/mle-demo?theme={$theme}&data_source={$dataSource}&use_xhr={$xhrInt}")
        ->assertNoJavaScriptErrors()

        ->assertPresent($inputSelector)

        // assert that upload button is initially enabled
        ->assertButtonEnabled($uploadButtonSelector);

    // test that it shows error when no file selected
    $page->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.upload_no_files'));

    // test that invalid mime types are rejected
    // TODO fix: fails
//    $page->attach($inputSelector, $this->getInvalidMimeTypeFixture())
//        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
//        ->waitForText(__('medialibrary-extensions::messages.upload_failed_due_to_invalid_mimetype'));


    $maxItems = config('medialibrary-extensions.max_items_in_shared_media_collections');
    for ($i = 0; $i < $maxItems; $i++) {
        // attach image file and submit and check if spinner shows and upload is successful
        $page->attach($inputSelector, $this->getRandomFixture())
            ->pressAndWaitFor($uploadButtonSelector, $waitTime)
            ->waitForText(__('medialibrary-extensions::messages.please_wait'))
            ->waitForText(__('medialibrary-extensions::messages.upload_success'));
    }

        // assert that the image is visible in the preview
        $page->assertPresent($gridSelector.' [data-test="media-preview-item"]:first-child')

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
        // TODO fix fails in plain theme
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
        ->pressAndWaitFor($mediaModalCloseButtonSelector, $waitTime);

        // delete media test
        for ($i = 0; $i < $maxItems; $i++) {
            // check delete media works
            $page->pressAndWaitFor($deleteButtonSelector, $waitTime)
                ->waitForText(__('medialibrary-extensions::messages.please_wait'))
                ->waitForText(__('medialibrary-extensions::messages.medium_removed'));

        }

        // the upload button should be enabled again
        $page->assertButtonEnabled($uploadButtonSelector);

    //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

})->group('browser')
    ->with('mmm_test_matrix')
    ->skip();
