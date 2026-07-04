<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\View\AnonymousComponent;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;

beforeEach(function () {
    // TODO fix: why do I need to do this?
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
    'bootstrap + default + no xhr + permanent' => ['bootstrap-5', 'default', false, 'permanent'], // TODO fails
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

// sometimes fails, seen it fail on the 3rd and 7th item
dataset('media_lab_test_matrix', [
    'bootstrap + default + xhr' => ['bootstrap-5', 'default', true],
        'bootstrap + default + no xhr' => ['bootstrap-5', 'default', false],

        'bootstrap + demo + xhr' => ['bootstrap-5', 'demo', true],// TODO sometimes fails
        'bootstrap + demo + no xhr' => ['bootstrap-5', 'demo', false],

        'plain + default + xhr' => ['plain', 'default', true],// TODO sometimes fails
        'plain + default + no xhr' => ['plain', 'default', false],

        'plain + demo + xhr' => ['plain', 'demo', true],
        'plain + demo + no xhr' => ['plain', 'demo', false],
]);

/**
 * Ensure there is exactly one medium available for the Media Lab preview.
 *
 * Preference order:
 *  - Reuse an existing upload from the Single manager collection ('alien-single-image') if present
 *  - Otherwise append the demo image to the 'alien-media-lab' collection
 *
 * All actions are executed on the correct connection resolved from the given data source.
 */
function ensureLabMedium(string $dataSource): void
{
    /** @var Alien $model */
    $model = new Alien;

    if ($dataSource !== '') {
        $connection = app(DataSourceResolver::class)->resolveConnection($dataSource);
        $model->setConnection($connection);
    }

    /** @var Alien $existingModel */
    $existingModel = $model->newQuery()->with('media')->first();
    if (! $existingModel) {
        $existingModel = $model->newQuery()->create();
    }

    // Resolve disk for the data source, fallback to 'demo'
    $mediaDisks = config('medialibrary-extensions.media_disks');
    $disk = $mediaDisks[$dataSource] ?? ($mediaDisks['demo'] ?? null);

    // If Lab already has media, ensure exactly one is present and its file exists on the expected disk.
    $labMedia = $existingModel->getMedia('alien-media-lab');
    if (! $labMedia->isEmpty()) {
        $current = $labMedia->first();

        // When a record exists but its file was cleaned or lives on a different disk, re-create deterministically.
        $fileExists = is_file($current->getPath());
        $onExpectedDisk = method_exists($current, 'disk') ? ($current->disk === $disk) : true;

        if ($fileExists && $onExpectedDisk) {
            return; // Healthy state
        }

        // Self-heal: reset the collection to a single known demo image on the expected disk.
        $existingModel->clearMediaCollection('alien-media-lab');

        $demoImage = __DIR__.'/../../resources/demo/demo_small.jpeg';
        if (is_file($demoImage)) {
            $existingModel
                ->addMedia($demoImage)
                ->preservingOriginal()
                ->toMediaCollection('alien-media-lab', $disk);
        }

        $existingModel->load('media');
        return;
    }

    // Prefer reusing a Single upload if available
    $single = $existingModel->getMedia('alien-single-image')->first();

    if ($single && is_file($single->getPath())) {
        $existingModel
            ->addMedia($single->getPath())
            ->preservingOriginal()
            ->toMediaCollection('alien-media-lab', $disk);

        $existingModel->load('media');

        return;
    }

    // Fallback: add the packaged demo image
    //    $demoImage = base_path('packages/mlbrgn/laravel-medialibrary-extensions/resources/demo/demo_small.jpeg');
    $demoImage = __DIR__.'/../../resources/demo/demo_small.jpeg';

    if (is_file($demoImage)) {
        $existingModel
            ->addMedia($demoImage)
            ->preservingOriginal()
            ->toMediaCollection('alien-media-lab', $disk);

        $existingModel->load('media');
    }

    // If still empty here, allow the DemoController fallback to create a demo image.
}

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

it('can control mms', function ($theme, $dataSource, $xhr, $storage) use ($waitTimeXhr, $waitTImeNonXhr) {

    // prepare selectors
    $mediaManagerId = '#alien-single-'.$storage.'-mms';
    $inputSelector = $mediaManagerId.' [data-mle-media-input]';
    $uploadButtonSelector = $mediaManagerId.' [data-mle-media-upload-button]';
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
    $waitTime = $xhr ? $waitTimeXhr : $waitTImeNonXhr;

    //    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt#$mediaManagerId")
    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $mediaManagerId);

    $page->assertPresent($inputSelector)

        // assert that the upload button is initially enabled
        ->assertButtonEnabled($uploadButtonSelector);

        // TODO check counts are correct
//        if (!$xhr) {
//            $page->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => 0, 'total' => 1]));
//        }

        // test that it shows error when no file selected
        $page->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.upload_no_files'))

        // test that invalid mime types are rejected
        ->attach($inputSelector, $this->getInvalidMimeTypeFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.upload_failed_due_to_invalid_mimetype'))

        // attach an image file and submit and check if spinner shows and upload is successful
        ->attach($inputSelector, $this->getRandomFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.upload_success'));

        // counts should update to 1 / 1 in single manager and show max alert
        // TODO test that media count is correct
//        ->wait(0.3)
//        ->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => 1, 'total' => 1]));


    if ($xhr) {
        $page->assertPresent($maxReachedAlertSelector);
    }

    // assert that the image is visible in the preview
    $page->assertPresent($gridSelector.' [data-mle-media-preview-item]:first-child');

    // assert that the upload button is disabled after upload (single media)
// FIXME
//        ->assertButtonDisabled($uploadButtonSelector)

    // assert that the image is visible in the preview
    //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

    // assert grid is present
    $page->assertPresent($gridSelector);

    // assert grid has the media container
    $page->assertPresent($firstMediaPreviewContainer);

    // check that the media item's menu has the expected buttons and state
    $page->assertButtonEnabled($editButtonSelector)
//     TODO not available in mms, should not be visible at all
//        ->assertMissing($setAsFirstButtonSelector)
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
        // check that media modal can be closed
        ->pressAndWaitFor($mediaModalCloseButtonSelector, $waitTime);

    // check image editor modal can be closed using the close button
    $page->pressAndWaitFor($editButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSelector)
        ->assertVisible($imageEditorModalSelector)
        ->assertDontSee(__('medialibrary-extensions::messages.could_not_initialize_image_editor'))
        ->pressAndWaitFor($imageEditorModalCloseButtonSelector, $waitTime)
        ->assertMissing($imageEditorModalSelector);

    // TODO, don't know how to check image editor modal can be closed using esc key
//    $page->pressAndWaitFor($editButtonSelector, $waitTime)
//        ->assertPresent($imageEditorModalSelector);
//        ->focus($imageEditorModalSelector)
//        ->keys($imageEditorModalSelector, '{esc}');

    // check saving edited image in the image editor
    $page->pressAndWaitFor($editButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSelector)
        ->assertVisible($imageEditorModalSelector)
        ->assertDontSee(__('medialibrary-extensions::messages.could_not_initialize_image_editor'))
        ->pressAndWaitFor($imageEditorModalRotateCcwButtonSelector, $waitTime)
        ->pressAndWaitFor($imageEditorModalSaveButtonSelector, $waitTime)
        ->assertMissing($imageEditorModalSelector);
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
    $page->pressAndWaitFor($deleteButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'));

    // TODO non-xhr does not show the delete message
    //        if ($xhr) {
    $page->waitForText(__('medialibrary-extensions::messages.medium_removed'));
    //        }

    // the upload button should be enabled again
    $page->assertButtonEnabled($uploadButtonSelector);

    // counts should be 0 / 1 and max alert should be gone after XHR delete
//    $page->assertSeeIn($countsSelector, '0 / 1');
    if ($xhr) {
        $page->assertMissing($maxReachedAlertSelector);
    }

    //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

})->group('browser')
    ->with('mms_test_matrix');


it('honors min / max width height and file size constraints in uploads', function ($theme, $dataSource, $xhr, $storage) use ($waitTimeXhr, $waitTImeNonXhr) {

    // prepare selectors
    $mediaManagerId = '#alien-single-'.$storage.'-mms';
    $inputSelector = $mediaManagerId.' [data-mle-media-input]';
    $uploadButtonSelector = $mediaManagerId.' [data-mle-media-upload-button]';

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $waitTimeXhr : $waitTImeNonXhr;

    //    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt#$mediaManagerId")
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
        ->waitForText(__('medialibrary-extensions::messages.image_too_small', ['width' => 16, 'height' => 16, 'min_width' => config('medialibrary-extensions.min_image_width'), 'min_height' => config('medialibrary-extensions.min_image_height')]));

        // test that an image that is too large is rejected
        config(['medialibrary-extensions.max_image_width' => 15]);
        config(['medialibrary-extensions.max_image_height' => 15]);
        $page->attach($inputSelector, $this->getTinyImageFixture())
            ->pressAndWaitFor($uploadButtonSelector, $waitTime)
            ->waitForText(__('medialibrary-extensions::messages.image_too_large', ['width' => 16, 'height' => 16, 'max_width' => config('medialibrary-extensions.max_image_width'), 'max_height' => config('medialibrary-extensions.max_image_height')]));

    // test that too large images (file size) are rejected
    config(['medialibrary-extensions.max_upload_size' => 1024]);
    $page->attach($inputSelector, $this->getRandomFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitwaitForText('must not be greater than 1 kilobytes');



})->group('browser')
    ->with('mms_test_matrix')
->only();

it('can control mmm', function ($theme, $dataSource, $xhr, $storage) use ($waitTimeXhr, $waitTImeNonXhr) {

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

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $waitTimeXhr : $waitTImeNonXhr;

    //    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt#$mediaManagerId")
    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $mediaManagerId);

    $page->assertPresent($inputSelector)

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

    // counts should reflect max, and upload should be disabled with an alert when at max
    $page->assertSeeIn($countsSelector, $maxItems.' / '.$maxItems);
    if ($xhr) {
        $page->assertPresent($maxReachedAlertSelector);
    }

    // assert that the image is visible in the preview
    $page->assertPresent($gridSelector.' [data-mle-media-preview-item]:first-child')

    // TODO fix: assert that the upload button is disabled after uploading maxItems
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
        ->pressAndWaitFor($mediaModalCloseButtonSelector, $waitTime);
    $this->scrollIntoView($page, $mediaManagerId);

    // check image editor modal can be opened and closed
    $page->pressAndWaitFor($editButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSelector)
//        ->wait(2)
        ->pressAndWaitFor($imageEditorModalCloseButtonSelector, $waitTime);
    //        ->wait(2);

    $this->scrollIntoView($page, $mediaManagerId);

    // delete one media and validate counts/alerts/form state
    $page->pressAndWaitFor($deleteButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.medium_removed'));

    $remaining = $maxItems - 1;
    $page->assertSeeIn($countsSelector, $remaining.' / '.$maxItems);
    if ($xhr) {
        $page->assertMissing($maxReachedAlertSelector);
    }
    $page->assertButtonEnabled($uploadButtonSelector);

    // delete the rest to ensure stability of the delete flow
    for ($i = 0; $i < $remaining; $i++) {
        $page->pressAndWaitFor($deleteButtonSelector, $waitTime)
            ->waitForText(__('medialibrary-extensions::messages.please_wait'))
            ->waitForText(__('medialibrary-extensions::messages.medium_removed'));
    }

    // the upload button should be enabled again
    $page->assertButtonEnabled($uploadButtonSelector);

    //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

})->group('browser')
    ->with('mmm_test_matrix');

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

    //    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt#$mediaManagerId")
    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $mediaManagerId);

    $page->assertPresent($inputSelector)

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

it('can control standalone media carousel', function ($theme, $dataSource, $xhr, $temporary = false, $uploadMedia = false) use ($waitTimeXhr, $waitTImeNonXhr) {

    // prepare MMM selectors to upload media first
    $mmmPermanentId = '#alien-multiple-permanent-mmm';
    $mmmPermanentInputSelector = $mmmPermanentId.' [data-mle-media-input]';
    $mmmPermanentUploadButtonSelector = $mmmPermanentId.' [data-mle-media-upload-button]';

    $mmmTemporaryId = '#alien-multiple-temporary-mmm';
    $mmmTemporaryInputSelector = $mmmTemporaryId.' [data-mle-media-input]';
    $mmmTemporaryUploadButtonSelector = $mmmTemporaryId.' [data-mle-media-upload-button]';

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
    //    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt#alien-carousel-crs")
    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $carouselId);

    // don't upload each iteration
    if ($uploadMedia) {

        if (!$temporary) {
            $this->scrollIntoView($page, $mmmPermanentId);

            // 1. Upload two images via MMM
            $page->attach($mmmPermanentInputSelector, $this->getRandomFixture())
                ->pressAndWaitFor($mmmPermanentUploadButtonSelector, $waitTime)
                ->waitForText(__('medialibrary-extensions::messages.upload_success'));

            $page->attach($mmmPermanentInputSelector, $this->getRandomFixture())
                ->pressAndWaitFor($mmmPermanentUploadButtonSelector, $waitTime)
                ->waitForText(__('medialibrary-extensions::messages.upload_success'));
        } else {
            $this->scrollIntoView($page, $mmmTemporaryId);

            // 1. Upload two images via MMM
            $page->attach($mmmTemporaryInputSelector, $this->getRandomFixture())
                ->pressAndWaitFor($mmmTemporaryUploadButtonSelector, $waitTime)
                ->waitForText(__('medialibrary-extensions::messages.upload_success'));

            $page->attach($mmmTemporaryInputSelector, $this->getRandomFixture())
                ->pressAndWaitFor($mmmTemporaryUploadButtonSelector, $waitTime)
                ->waitForText(__('medialibrary-extensions::messages.upload_success'));
        }

    }

    // 2. Refresh the page to see them in Carousel
    $page->refresh();

    $this->scrollIntoView($page, $carouselId);

    $page->assertPresent($carouselId)
        ->assertPresent($carouselId)
        ->assertPresent($indicatorsSelector)
        ->assertPresent($nextButtonSelector)
        ->assertPresent($prevButtonSelector)
        ->assertPresent($firstItemSelector)
        ->assertAttributeContains($firstItemSelector, 'class', 'active')

        // click next
        ->click($nextButtonSelector)
//        ->wait(0.5) // Wait for transition
        ->assertAttributeContains($secondItemSelector, 'class', 'active')
//        ->assertAttributeMissing($firstItemSelector, 'class', 'active')

        // click prev
        ->click($prevButtonSelector)
//        ->wait(0.5) // Wait for transition
        ->assertAttributeContains($firstItemSelector, 'class', 'active')
//        ->assertAttributeMissing($secondItemSelector, 'class', 'active')

        // click the indicator for the second item
        ->click($indicatorsSelector.' [data-mle-slide-to="1"]')
//        ->wait(0.5) // Wait for transition
        ->assertAttributeContains($secondItemSelector, 'class', 'active')

        // test modal expansion if applicable (default is true)
//        ->click($secondItemSelector.' [data-mle-modal-trigger]')
        ->click($secondItemSelector)
//        ->wait(0.5)
        ->assertPresent($modalSelector)
        ->click($modalCloseButtonSelector)
//        ->wait(0.5)
        ->assertMissing($modalSelector); // not visible

})->group('browser')
    ->with([
        'bootstrap + default + xhr + permanent' => ['bootstrap-5', 'default', true, false, true],
        'bootstrap + demo + no xhr + permanent' => ['bootstrap-5', 'demo', false, false, true],

        'bootstrap + default + xhr + temporary' => ['bootstrap-5', 'default', true, true, true],
        'bootstrap + demo + no xhr + temporary' => ['bootstrap-5', 'demo', false, true, true],

        'plain + default + xhr + permanent' => ['plain', 'default', true, false, false],
        'plain + demo + no xhr + permanent' => ['plain', 'demo', false, false, false],

        'plain + default + xhr + temporary' => ['plain', 'default', true, true, false],
        'plain + demo + no xhr + temporary' => ['plain', 'demo', false, true, false],
    ]);

it('can control media lab', function ($theme, $dataSource, $xhr, $uploadMedia = false) use ($waitTimeXhr, $waitTImeNonXhr) {

    // prepare MMM selectors to upload media first
    $mmmId = '#alien-multiple-permanent-mmm';
    $mmmInputSelector = $mmmId.' [data-mle-media-input]';
    $mmmUploadButtonSelector = $mmmId.' [data-mle-media-upload-button]';

    // prepare media lab selectors
    $labId = '#alien-laboratory-lab';
    //    $labSelector = $labId.'[data-mle-media-lab]';
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

    // Ensure the Media Lab has a medium to work with, using the correct data source
    ensureLabMedium($dataSource);

    //    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt#alien-laboratory-lab")
    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $labId);
    // don't upload each iteration
    //        if ($uploadMedia) {
    //            // 1. Upload two images via MMM
    //            $page->attach($mmmInputSelector, $this->getRandomFixture())
    //                ->pressAndWaitFor($mmmUploadButtonSelector, $waitTime)
    //                ->waitForText(__('medialibrary-extensions::messages.upload_success'));
    //
    //            $page->attach($mmmInputSelector, $this->getRandomFixture())
    //                ->pressAndWaitFor($mmmUploadButtonSelector, $waitTime)
    //                ->waitForText(__('medialibrary-extensions::messages.upload_success'));
    //        }

    $page->assertPresent($labId)
//            ->scrollTo($labId)
        ->assertPresent($labOriginalSelector)
        ->assertPresent($labBaseSelector)
        ->assertPresent($mmsSelector)
//            ->wait(2)
            ->wait(1)// needed because JavaScript uses error to see if image can be loaded, this might take some time
        ->assertDontSee('Image loading / decoding failed')

    // 3. Test image editor via nested MMS in Lab
        ->pressAndWaitFor($mmsEditButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSelector)
        ->pressAndWaitFor($imageEditorModalRotateCcwButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSaveButtonSelector)
        ->pressAndWaitFor($imageEditorModalSaveButtonSelector, $waitTime)

//        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->assertMissing($imageEditorModalSelector)

    // After save, ensure Base and Original reference the same new medium id
    // We read the medium id from the restore button data attribute in the Original panel,
    // and from the nested MMS Single component button/link in the Base panel if available.
    // Fallback: use the restore button as source of truth and assert Base has updated too by
    // checking that the restore form's data route contains the same id and that the Base panel exists.
//        ->assertPresent($restoreButtonSelector)
//        ->wait(0.2)
//        ->tap(function ($page) use ($restoreButtonSelector, $labBaseSelector) {
//            // Extract the new medium id from the Original restore button
//            $newMediumId = $page->attribute($restoreButtonSelector, 'data-mle-medium-id');
//
//            expect(is_numeric($newMediumId))->toBeTrue("Expected numeric medium id after save");
//
//            // Also ensure the restore route reflects the same id
//            $restoreRoute = $page->attribute($restoreButtonSelector, 'data-mle-route');
//            expect($restoreRoute)->toContain("/{$newMediumId}");
//
//            // Within Base preview, ensure the nested media manager is re-rendered (sanity)
//            $page->assertPresent($labBaseSelector);
//        })

    // 4. Test restore original (only if not temporary, and the demo page uses permanent here)
        ->assertPresent($restoreButtonSelector)
        // TODO why do i need the refresh, if i don't refresh i get medium not found!
//        ->refresh()
        ->pressAndWaitFor($restoreButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'));

    //    if($xhr) {
    $page->waitForText(__('medialibrary-extensions::messages.restored_original'));
    //    }

    // TODO fix
    //        ->waitForText(__('medialibrary-extensions::messages.restored_original'));

})->group('browser')
    ->with('media_lab_test_matrix');
