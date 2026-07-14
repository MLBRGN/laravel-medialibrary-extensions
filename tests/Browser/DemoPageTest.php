<?php

/** @noinspection InvalidDatasetNameCaseInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

use Illuminate\Support\Facades\Config;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;
use Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure;
use Pest\Browser\Api\AwaitableWebpage;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

$waitTimeXhr = .1;
$waitTImeNonXhr = 1; // non-xhr tests are slower, setting it to lower than 1 may cause too many failures

dataset('mms_test_matrix', [
    'bootstrap + demo default + xhr + permanent' => ['bootstrap-5', 'demo_default', true, 'permanent'],
    'bootstrap + demo default + xhr + temporary' => ['bootstrap-5', 'demo_default', true, 'temporary'],
    'bootstrap + demo default + no xhr + permanent' => ['bootstrap-5', 'demo_default', false, 'permanent'],
    'bootstrap + demo default + no xhr + temporary' => ['bootstrap-5', 'demo_default', false, 'temporary'],

    'bootstrap + demo alt + xhr + permanent' => ['bootstrap-5', 'demo_alt', true, 'permanent'],
    'bootstrap + demo alt + xhr + temporary' => ['bootstrap-5', 'demo_alt', true, 'temporary'],
    'bootstrap + demo alt + no xhr + permanent' => ['bootstrap-5', 'demo_alt', false, 'permanent'],
    'bootstrap + demo alt + no xhr + temporary' => ['bootstrap-5', 'demo_alt', false, 'temporary'],// sometimes times out

    'plain + demo default + xhr + permanent' => ['plain', 'demo_default', true, 'permanent'],
    'plain + demo default + xhr + temporary' => ['plain', 'demo_default', true, 'temporary'],
    'plain + demo default + no xhr + permanent' => ['plain', 'demo_default', false, 'permanent'],
    'plain + demo default + no xhr + temporary' => ['plain', 'demo_default', false, 'temporary'], // saw this test failing when running in full test for "it honors min /...."

    'plain + demo alt + xhr + permanent' => ['plain', 'demo_alt', true, 'permanent'],
    'plain + demo alt + xhr + temporary' => ['plain', 'demo_alt', true, 'temporary'],
    'plain + demo alt + no xhr + permanent' => ['plain', 'demo_alt', false, 'permanent'],
    'plain + demo alt + no xhr + temporary' => ['plain', 'demo_alt', false, 'temporary'], // saw this test failing when running in full test for "it honors min /...."
]);

dataset('mmm_test_matrix', [
    'bootstrap + demo default + xhr + permanent' => ['bootstrap-5', 'demo_default', true, 'permanent'],
    'bootstrap + demo default + xhr + temporary' => ['bootstrap-5', 'demo_default', true, 'temporary'],
    'bootstrap + demo default + no xhr + permanent' => ['bootstrap-5', 'demo_default', false, 'permanent'],// fails (no files uploaded? when not the first test)
    'bootstrap + demo default + no xhr + temporary' => ['bootstrap-5', 'demo_default', false, 'temporary'],// fails (no files uploaded? when not the first test)

    'bootstrap + demo alt + xhr + permanent' => ['bootstrap-5', 'demo_alt', true, 'permanent'],
    'bootstrap + demo alt + xhr + temporary' => ['bootstrap-5', 'demo_alt', true, 'temporary'],
    'bootstrap + demo alt + no xhr + permanent' => ['bootstrap-5', 'demo_alt', false, 'permanent'],// fails invalid mimetyoe shown counts not matching
    'bootstrap + demo alt + no xhr + temporary' => ['bootstrap-5', 'demo_alt', false, 'temporary'],

    'plain + demo default + xhr + permanent' => ['plain', 'demo_default', true, 'permanent'],
    'plain + demo default + xhr + temporary' => ['plain', 'demo_default', true, 'temporary'],
    'plain + demo default + no xhr + permanent' => ['plain', 'demo_default', false, 'permanent'],
    'plain + demo default + no xhr + temporary' => ['plain', 'demo_default', false, 'temporary'],

    'plain + demo alt + xhr + permanent' => ['plain', 'demo_alt', true, 'permanent'],
    'plain + demo alt + xhr + temporary' => ['plain', 'demo_alt', true, 'temporary'],
    'plain + demo alt + no xhr + permanent' => ['plain', 'demo_alt', false, 'permanent'],
    'plain + demo alt + no xhr + temporary' => ['plain', 'demo_alt', false, 'temporary'],
]);

dataset('mms_youtube_test_matrix', [
    'bootstrap + demo default + xhr + permanent' => ['bootstrap-5', 'demo_default', true, 'permanent'],
    'bootstrap + demo default + xhr + temporary' => ['bootstrap-5', 'demo_default', true, 'temporary'],
    'bootstrap + demo default + no xhr + permanent' => ['bootstrap-5', 'demo_default', false, 'permanent'], // TODO fails
    'bootstrap + demo default + no xhr + temporary' => ['bootstrap-5', 'demo_default', false, 'temporary'],

    'bootstrap + demo alt + xhr + permanent' => ['bootstrap-5', 'demo_alt', true, 'permanent'],
    'bootstrap + demo alt + xhr + temporary' => ['bootstrap-5', 'demo_alt', true, 'temporary'],
    'bootstrap + demo alt + no xhr + permanent' => ['bootstrap-5', 'demo_alt', false, 'permanent'],
    'bootstrap + demo alt + no xhr + temporary' => ['bootstrap-5', 'demo_alt', false, 'temporary'],

    'plain + demo default + xhr + permanent' => ['plain', 'demo_default', true, 'permanent'],
    'plain + demo default + xhr + temporary' => ['plain', 'demo_default', true, 'temporary'],
    'plain + demo default + no xhr + permanent' => ['plain', 'demo_default', false, 'permanent'],
    'plain + demo default + no xhr + temporary' => ['plain', 'demo_default', false, 'temporary'],

    'plain + demo alt + xhr + permanent' => ['plain', 'demo_alt', true, 'permanent'],
    'plain + demo alt + xhr + temporary' => ['plain', 'demo_alt', true, 'temporary'],
    'plain + demo alt + no xhr + permanent' => ['plain', 'demo_alt', false, 'permanent'],
    'plain + demo alt + no xhr + temporary' => ['plain', 'demo_alt', false, 'temporary'],
]);

dataset('media_lab_test_matrix', [
    'bootstrap + demo default + xhr' => ['bootstrap-5', 'demo_default', true],
    'bootstrap + demo default + no xhr' => ['bootstrap-5', 'demo_default', false],

    'bootstrap + demo alt + xhr' => ['bootstrap-5', 'demo_alt', true],
    'bootstrap + demo alt + no xhr' => ['bootstrap-5', 'demo_alt', false],

    'plain + demo default + xhr' => ['plain', 'demo_default', true],
    'plain + demo default + no xhr' => ['plain', 'demo_default', false],

    'plain + demo alt + xhr' => ['plain', 'demo_alt', true],
    'plain + demo alt + no xhr' => ['plain', 'demo_alt', false],
]);

dataset('media_html_editor_matrix', [
    //    'bootstrap + demo default + xhr' => ['bootstrap-5', 'demo_default', true],
    //    'bootstrap + demo default + no xhr' => ['bootstrap-5', 'demo_default', false],

    //    'bootstrap + demo alt + xhr' => ['bootstrap-5', 'demo_alt', true],
    //    'bootstrap + demo alt + no xhr' => ['bootstrap-5', 'demo_alt', false],

    'plain + demo default + xhr' => ['plain', 'demo_default', true],
    //    'plain + demo default + no xhr' => ['plain', 'demo_default', false],

    //    'plain + demo alt + xhr' => ['plain', 'demo_alt', true],
    //    'plain + demo alt + no xhr' => ['plain', 'demo_alt', false],
]);

dataset('media_carousel_test_matrix',
    [
        'bootstrap + demo default + xhr + permanent' => ['bootstrap-5', 'demo_default', true, false, true],
        'bootstrap + demo alt + no xhr + permanent' => ['bootstrap-5', 'demo_alt', false, false, true],

        'bootstrap + demo default + xhr + temporary' => ['bootstrap-5', 'demo_default', true, true, true],
        'bootstrap + demo alt + no xhr + temporary' => ['bootstrap-5', 'demo_alt', false, true, true],

        'plain + demo default + xhr + permanent' => ['plain', 'demo_default', true, false, false],
        'plain + demo alt + no xhr + permanent' => ['plain', 'demo_alt', false, false, false],

        'plain + demo default + xhr + temporary' => ['plain', 'demo_default', true, true, false],
        'plain + demo alt + no xhr + temporary' => ['plain', 'demo_alt', false, true, false],
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

    // Resolve disk for the data source, fallback to 'demo_alt'
    $disk = \Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure::disk('demo');

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

    // fallback
    $demoImage = __DIR__.'/../../resources/demo/demo_small.jpeg';

    if (is_file($demoImage)) {
        $existingModel
            ->addMedia($demoImage)
            ->preservingOriginal()
            ->toMediaCollection('alien-media-lab', $disk);

        $existingModel->load('media');
    }
}

it('loads required assets', function () {

    $this->visit('/mle-demo')
        ->assertNoJavaScriptErrors();

    $laravelMedialibraryExtensions = 'laravel-medialibrary-extensions';
    $laravelFormComponents = 'laravel-form-components';

    // testing a selection of different assets:

    // Core JS
    $this->get("/vendor/mlbrgn/{$laravelMedialibraryExtensions}/js/core/media-library-loader.js")
        ->assertSuccessful();

    // Verify theme-specific assets
    $this->get("/vendor/mlbrgn/{$laravelMedialibraryExtensions}/css/bootstrap-5.css")
        ->assertSuccessful();

    $this->get("/vendor/mlbrgn/{$laravelMedialibraryExtensions}/js/bootstrap-5.js")
        ->assertSuccessful();

    // Verify image editor
    $this->get("/vendor/mlbrgn/{$laravelMedialibraryExtensions}/js/image-editor.js")
        ->assertSuccessful();

    // Verify tinymce
    $this->get("/vendor/mlbrgn/{$laravelMedialibraryExtensions}/js/shared/tinymce-custom-file-picker.js")
        ->assertSuccessful();

    // Verify form components
    $this->get("/vendor/mlbrgn/{$laravelFormComponents}/js/html-editor.js")
        ->assertSuccessful();

})->group('browser');

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
        ->assertQueryStringHas('data_source', 'demo_default')
        ->click('@btn-data-source-demo')
        ->assertQueryStringHas('data_source', 'demo_alt')

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
    $waitTime = $xhr ? $waitTimeXhr : $waitTImeNonXhr;

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

    // counts should update to 1 of 1 and show max alert
    $page->wait(0.3)
        ->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => 1, 'total' => 1]));

    $page->assertPresent($maxReachedAlertSelector);

    // assert that the image is visible in the preview
    $page->assertPresent($gridSelector.' [data-mle-media-preview-item]:first-child');

    // assert that the upload button is disabled after upload (single media)
    $page->assertButtonDisabled($uploadButtonSelector);
    $page->assertButtonDisabled($uploadButtonYouTubeSelector);

    // assert that the image is visible in the preview
    // TODO causes Call to undefined method Tests\Browser\DemoPageTest::assertPreviewImageVisible()
    //        $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

    // assert grid is present
    $page->assertPresent($gridSelector);

    // assert grid has the media container
    $page->assertPresent($firstMediaPreviewContainer);

    // check that the media item's menu has the expected buttons and state
    // TODO set as first should not be available in mms at all, should not be visible at all using->assertMissing($setAsFirstButtonSelector)
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

    // TODO, don't know how to check that the media modal can be closed using esc key
    //    if ($theme === 'plain') {
    //        $page->pressAndWaitFor($mediaPreviewImageSelector, $waitTime)
    //                ->assertPresent($mediaModalSelector)
    //    //        ->click($mediaModalSelector)
    //    //        ->keys('body', ['{ESCAPE}']);
    //    //        ->keys($mediaModalSelector, ['{Escape}']);
    //    //        ->keys($mediaModalSelector, ['{ESC}']);
    //    //        ->keys($mediaModalSelector, ['{esc}']);
    //    //        ->keys($mediaModalSelector, ['{escape}']);
    //    //        ->keys('body', ['Escape']);
    //            ->keys('body', ['Escape']);
    //    }

    // check image editor modal can be closed using the close button
    $page->pressAndWaitFor($editButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSelector)
        ->assertVisible($imageEditorModalSelector)
        ->assertDontSee(__('medialibrary-extensions::messages.could_not_initialize_image_editor'))
        ->pressAndWaitFor($imageEditorModalCloseButtonSelector, $waitTime)
        ->assertMissing($imageEditorModalSelector);

    // TODO, don't know how to check that the image editor modal can be closed using esc key
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

    // TODO fails with dataset "plain + demo default + no xhr + permanent"
    //        ->waitForText(__('medialibrary-extensions::messages.medium_replaced'));

    // check canceling image editing in the image editor
    // TODO image editor modal was not closed after canceling
    //    ->pressAndWaitFor($editButtonSelector, $waitTime)
    //    ->assertVisible($imageEditorModalSelector)
    //    ->assertDontSee(__('medialibrary-extensions::messages.could_not_initialize_image_editor'))
    //    ->pressAndWaitFor($imageEditorModalCancelButtonSelector, $waitTime)
    //    ->assertMissing($imageEditorModalSelector)

    // check delete media works
    $page->pressAndWaitFor($deleteButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.medium_removed'));

    // the upload button should be enabled again
    $page->assertButtonEnabled($uploadButtonSelector);

    // max alert should be gone after XHR delete
    $page->assertMissing($maxReachedAlertSelector);

    // TODO
    //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

})->group('browser')
    ->with('mms_test_matrix');
//    ->flaky();

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
    // NOTE saw this test failing when running in full test with Expected to see text [The image is too small (16x16). Minimum required is 320x160.] on the page initially with the url [http://127.0.0.1:64169/mle-demo?theme=plain&data_source=default&use_xhr=0], but it was not found or not visible. A screenshot of the page has been saved to [Tests/Browser/Screenshots/it_honors_min___max_width_height_and_file_size_constraints_in_uploads].
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
        ->waitForText('must not be greater than 1 kilobytes');

})->group('browser')
    ->with('mms_test_matrix')
    ->flaky();

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

//    $connection = DataSourceResolver::resolve('demo', 'default');
    $connection = PackageInfrastructure::connection('demo', 'default');

    expect(
        DB::connection($connection)->table('media')->count()
    )->toBe(0);

    expect(
        DB::connection($connection)->table('mle_temporary_uploads')->count()
    )->toBe(0);

    //    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt#$mediaManagerId")
    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $mediaManagerId);

    // assert that the upload button is initially enabled
    $page->assertPresent($inputSelector)
        ->assertButtonEnabled($uploadButtonSelector);

    // test that it shows error when no file selected
    $page->pressAndWaitFor($uploadButtonSelector, $waitTime);

    if (!$xhr) {
        $page->wait($waitTime);
    }
    $page->waitForText(__('medialibrary-extensions::messages.upload_no_files'));

    // TODO test that invalid mime types are rejected
    $page->attach($inputSelector, $this->getInvalidMimeTypeFixture())
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.upload_failed_due_to_invalid_mimetype'));

    $maxItems = config('medialibrary-extensions.max_items_in_shared_media_collections');

    // check counts start at 0
    $page->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => 0, 'total' => $maxItems]));

    for ($i = 0; $i < $maxItems; $i++) {
        // attach an image file and submit and check if spinner shows and upload is successful
        $page->attach($inputSelector, $this->getRandomFixture());
        if (!$xhr) {
            $page->wait($waitTime);
        }

        $page->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.upload_success'));


        // TODO sometimes fails on non-xhr, but not on xhr
        if (!$xhr) {
            // counts should update to 1 of 1 and show max alert
            $page->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => $i + 1, 'total' => $maxItems]));

        }
    }

    // counts should reflect max, and upload should be disabled with an alert when at max
    $page->assertPresent($maxReachedAlertSelector);

    // assert that the image is visible in the preview
    $page->assertPresent($gridSelector.' [data-mle-media-preview-item]:first-child')

//         TODO fix: assert that the upload button is disabled after uploading maxItems
        ->assertButtonDisabled($uploadButtonSelector)

        // TODO assert that the image is visible in the preview
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

    // check image editor modal can be opened and closed
    $page->pressAndWaitFor($editButtonSelector, $waitTime)
        ->assertPresent($imageEditorModalSelector)
        ->pressAndWaitFor($imageEditorModalCloseButtonSelector, $waitTime);

    // delete one media and validate counts/alerts/form state
    $page->pressAndWaitFor($deleteButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.medium_removed'))
        ->assertMissing($maxReachedAlertSelector)
        ->assertButtonEnabled($uploadButtonSelector);

    $remaining = $maxItems - 1;
    // delete the rest to ensure stability of the delete flow
    for ($i = 0; $i < $remaining - 1; $i++) {
        $currentDeleteButtonSelector =
            $gridSelector.
            ' [data-mle-media-preview-container]:first-child [data-mle-media-delete-button]';
        $page->pressAndWaitFor($currentDeleteButtonSelector, $waitTime);
            if (!$xhr) {
                $page->wait($waitTime);
            }
            $page->waitForText(__('medialibrary-extensions::messages.please_wait'))
            ->waitForText(__('medialibrary-extensions::messages.medium_removed'));

        // counts check (NOTE: 1 is deleted outside the loop)
        if (!$xhr) {
            $page->wait($waitTime);
        }
        // TODO non-xhr fails here
        if ($xhr) {
            $page->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => $maxItems - $i - 2, 'total' => $maxItems]));
        }
    }

    // the upload button should be enabled again
    $page->assertButtonEnabled($uploadButtonSelector);

    // TODO
    //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

})->group('browser')
    ->with('mmm_test_matrix');
//    ->flaky();

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
        // TODO fails with dataset "bootstrap + demo default + no xhr + permanent"
        ->assertButtonDisabled($uploadButtonSelector)

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

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $carouselId);

    // don't upload each iteration
    if ($uploadMedia) {

        if (! $temporary) {
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
        ->assertAttributeContains($secondItemSelector, 'class', 'active')
//        ->assertAttributeMissing($firstItemSelector, 'class', 'active')

        // click prev
        ->click($prevButtonSelector)
        ->assertAttributeContains($firstItemSelector, 'class', 'active')
//        ->assertAttributeMissing($secondItemSelector, 'class', 'active')

        // click the indicator for the second item
        ->click($indicatorsSelector.' [data-mle-slide-to="1"]')
        ->assertAttributeContains($secondItemSelector, 'class', 'active')

        // test modal expansion if applicable (default is true)
//        ->click($secondItemSelector.' [data-mle-modal-trigger]')
        ->click($secondItemSelector)
        ->assertPresent($modalSelector)
        ->click($modalCloseButtonSelector)
        ->assertMissing($modalSelector); // not visible

})->group('browser')
    ->with('media_carousel_test_matrix');

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

    $page->assertPresent($labId)
        ->assertPresent($labOriginalSelector)
        ->assertPresent($labBaseSelector)
        ->assertPresent($mmsSelector)
        ->wait(.2)// needed because JavaScript uses error to see if the image can be loaded, this might take some time
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
        // Fallback: use the restore button as the source of truth and assert Base has updated too by
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
        ->pressAndWaitFor($restoreButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'));

    $page->waitForText(__('medialibrary-extensions::messages.restored_original'));

})->group('browser')
    ->with('media_lab_test_matrix');

it('can control html editor\'s custom file picker', function ($theme, $dataSource, $xhr, $uploadMedia = false) use ($waitTimeXhr, $waitTImeNonXhr) {

    //    config(['app.url' => 'http://127.0.0.1:53665']);

    $imageButton = '[data-mce-name="image"]';
    $saveButtonSelector = '[data-mce-name="Save"]';
    $cancelButtonSelector = '[data-mce-name="Cancel"]';
    $browseFilesButtonSelector = '[data-mce-name="Browse files"]';

    //        $insertSelectedButtonSelector = '[data-mle-insert-selected]';

    // tinyMCE selectors
    $iframeSelector = '.tox-dialog-wrap iframe';

    $mediaManagerId = '#media-manager-mmm';
    $inputSelector = $mediaManagerId.' [data-mle-media-input]';
    $uploadButtonSelector = $mediaManagerId.' [data-mle-media-upload-button]';

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $waitTimeXhr : $waitTImeNonXhr;

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

    // TODO open image picker and save
    //    $page->pressAndWaitFor($imageButton, $waitTime);
    //    $page->pressAndWaitFor($saveButtonSelector, $waitTime);

    // open the image picker and open the file picker
    $page->pressAndWaitFor($imageButton, $waitTime);
    $page->pressAndWaitFor($browseFilesButtonSelector, $waitTime);
    $page->assertPresent('.tox-dialog-wrap');
    $page->assertPresent($iframeSelector);

    $page->withinFrame($iframeSelector, function (AwaitableWebpage $page) use ($waitTime) {

        //        Config::set('medialibrary-extensions.max_items_in_shared_media_collections', 3);

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
        $imageEditorModalSaveButtonSelector = $imageEditorModalSelector.' [data-click-action="save"]';

        $page->assertPresent($inputSelector)

            // assert that the upload button is initially enabled
            ->assertButtonEnabled($uploadButtonSelector);

        // test that it shows error when no file selected
        $page->pressAndWaitFor($uploadButtonSelector, $waitTime)
            ->waitForText(__('medialibrary-extensions::messages.upload_no_files'));

        // test that invalid mime types are rejected
        $page->attach($inputSelector, $this->getInvalidMimeTypeFixture())
            ->pressAndWaitFor($uploadButtonSelector, $waitTime)
            ->waitForText(__('medialibrary-extensions::messages.upload_failed_due_to_invalid_mimetype'));

        $maxItems = config('medialibrary-extensions.max_items_in_shared_media_collections');
        $maxItems = 3;

        // check counts start at 0
        //        $page->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => 0, 'total' => $maxItems]));

        for ($i = 0; $i < $maxItems; $i++) {
            // attach an image file and submit and check if spinner shows and upload is successful
            $page->attach($inputSelector, $this->getRandomFixture())
                ->pressAndWaitFor($uploadButtonSelector, $waitTime)
                ->waitForText(__('medialibrary-extensions::messages.please_wait'))
                ->waitForText(__('medialibrary-extensions::messages.upload_success'));

            // counts should update to 1 of 1 and show max alert
            //            $page->wait(0.3)
            //                ->assertSeeIn($countsSelector, __('medialibrary-extensions::messages.media_counts', ['current' => $i + 1, 'total' => $maxItems]));

        }

        // counts should reflect max, and upload should be disabled with an alert when at max
        //        $page->assertPresent($maxReachedAlertSelector);

        // assert that the image is visible in the preview
        $page->assertPresent($gridSelector.' [data-mle-media-preview-item]:first-child')

//         TODO fix: assert that the upload button is disabled after uploading maxItems
//            ->assertButtonDisabled($uploadButtonSelector)

            // TODO assert that the image is visible in the preview
            //    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

            // assert grid is present
            ->assertPresent($gridSelector)

            // assert grid has the media container
            ->assertPresent($firstMediaPreviewContainer)

            // check that the media item's menu has the expected buttons and state
            ->assertButtonEnabled($editButtonSelector)
            ->assertButtonDisabled($setAsFirstButtonSelector)
            ->assertButtonEnabled($deleteButtonSelector);

        // check media modal opening and presence of expected elements
        //            $page->assertPresent($mediaPreviewImageSelector)
        //            ->pressAndWaitFor($mediaPreviewImageSelector, $waitTime);

        // delete one media and validate counts/alerts/form state
        $page->pressAndWaitFor($deleteButtonSelector, $waitTime)
            ->waitForText(__('medialibrary-extensions::messages.please_wait'))
            ->waitForText(__('medialibrary-extensions::messages.medium_removed'));

        // select the first item
        $firstItemSelectSelector = $firstMediaPreviewContainer.' [data-mle-media-select-wrapper]';
        $page->assertPresent($firstItemSelectSelector);
        $page->click($firstItemSelectSelector);
        $page->wait(.5);

        // click insert selected media
        $insertSelectedButtonSelector = '[data-mle-insert-selected]';
        $page->pressAndWaitFor($insertSelectedButtonSelector, $waitTime);
        $page->wait(.5);

        // TODO modal opening not working in test
        //            ->assertVisible($mediaModalSelector)
        //            ->assertPresent($mediaModalSelector)
        //            ->assertPresent($mediaModalCloseButtonSelector)
        //            ->assertPresent($mediaModalCarouselSelector)
        //            ->assertPresent($mediaModalCarouselIndicatorSelector)
        //            ->assertPresent($mediaModalCarouselItemSelector);

        // check that media modal can be closed
        //            ->pressAndWaitFor($mediaModalCloseButtonSelector, $waitTime);

        // TODO check image editor modal can be opened and closed
        //        $page->pressAndWaitFor($editButtonSelector, $waitTime)
        //            ->assertPresent($imageEditorModalSelector)
        //            ->pressAndWaitFor($imageEditorModalCloseButtonSelector, $waitTime);

        //        $page->assertMissing($maxReachedAlertSelector);

        //        $page->assertButtonEnabled($uploadButtonSelector);

    });

    $page->pressAndWaitFor($saveButtonSelector, $waitTime);
    $page->wait(.5);

    $tinyMceIframeSelector = '.tox-edit-area__iframe';
    $page->assertPresent($tinyMceIframeSelector);
    $page->withinFrame($tinyMceIframeSelector, function (AwaitableWebpage $page) {
        $tinyMceBodySelector = '#tinymce';
        $page->assertPresent($tinyMceBodySelector);
        $tinyMceBodyImgSelector = $tinyMceBodySelector.' img';
        $page->assertPresent($tinyMceBodyImgSelector);
    });

})->group('browser')
    ->with('media_html_editor_matrix');

it('promotes temporary uploads to permanent media on form submit', function () use ($waitTimeXhr) {
    $theme = 'bootstrap-5';
    $dataSource = 'demo_default';
    $xhr = true;
    $xhrInt = 1;
//    $waitTime = $waitTimeXhr;
    $waitTime = 1;

    $mmmTemporaryId = '#alien-multiple-temporary-mmm';
    $mmmTemporaryInputSelector = $mmmTemporaryId.' [data-mle-media-input]';
    $mmmTemporaryUploadButtonSelector = $mmmTemporaryId.' [data-mle-media-upload-button]';
    $mmmTemporaryFormSubmitSelector = $mmmTemporaryId.' + button[type="submit"]';

    $mmmPermanentId = '#alien-multiple-permanent-mmm';
    $mmmPermanentGridSelector = $mmmPermanentId.' [data-mle-media-preview-grid]';

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $mmmTemporaryId);

    // 1. Upload an image to temporary MMM
    $page->attach($mmmTemporaryInputSelector, $this->getRandomFixture())
        ->pressAndWaitFor($mmmTemporaryUploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.upload_success'));

    // 2. Submit the form to create the model and promote media
    // We need to click the specific "Save model" button for the MMM form
    $page->press($mmmTemporaryId.' ~ button[type="submit"]')
        ->wait(1)
        ->assertPathIs('/mle-demo')
        ->wait(1);

    // 3. Verify it appears in the permanent MMM
    $this->scrollIntoView($page, $mmmPermanentId);
    $page->wait(1)
        ->assertPresent($mmmPermanentGridSelector.' [data-mle-media-preview-container]:first-child [data-mle-media-preview-item] [data-mle-media-preview-image]');
})->group('browser')->todo();

it('promotes multiple temporary uploads to permanent media on form submit (MMM temporary)', function () use ($waitTimeXhr) {
    // Allow multiple items in the shared collection for this test
    Config::set('medialibrary-extensions.max_items_in_shared_media_collections', 3);
    $theme = 'bootstrap-5';
    $dataSource = 'demo_default';
    $xhrInt = 1;
//    $waitTime = $waitTimeXhr;
    $waitTime = 1;

    $mmmTemporaryId = '#alien-multiple-temporary-mmm';
    $mmmTemporaryInputSelector = $mmmTemporaryId.' [data-mle-media-input]';
    $mmmTemporaryUploadButtonSelector = $mmmTemporaryId.' [data-mle-media-upload-button]';

    $mmmPermanentId = '#alien-multiple-permanent-mmm';
    $mmmPermanentGridSelector = $mmmPermanentId.' [data-mle-media-preview-grid]';

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $mmmTemporaryId);

    // 1. Upload two images to temporary MMM
    $page->attach($mmmTemporaryInputSelector, $this->getRandomFixture())
        ->pressAndWaitFor($mmmTemporaryUploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.upload_success'));

    $page->attach($mmmTemporaryInputSelector, $this->getRandomFixture())
        ->pressAndWaitFor($mmmTemporaryUploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.upload_success'));

    // 2. Submit the specific MMM form's save button to create the model and promote media
    // Use pressAndWaitFor to allow time for the POST + redirect to complete before asserting the path.
    $page->pressAndWaitFor($mmmTemporaryId.' ~ button[type="submit"]', $waitTime)
        ->wait(0.6)
        ->assertPathIs('/mle-demo')
        ->wait(1);

    // 3. Verify at least two items appear in the permanent MMM grid
    $this->scrollIntoView($page, $mmmPermanentId);
    $page->assertPresent($mmmPermanentGridSelector);
    $page->assertPresent($mmmPermanentGridSelector.' [data-mle-media-preview-container]:first-child [data-mle-media-preview-item] [data-mle-media-preview-image]');
    $page->assertPresent($mmmPermanentGridSelector.' [data-mle-media-preview-container]:nth-child(2) [data-mle-media-preview-item] [data-mle-media-preview-image]');
})->group('browser')->todo();
