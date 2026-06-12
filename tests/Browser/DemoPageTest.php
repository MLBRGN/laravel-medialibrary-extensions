<?php

use Illuminate\Support\Facades\Blade;
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

$waitTime = .2;

dataset('test_matrix', [
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

it('can visit demo page switch theme, XHR and DataSource', function () {

    $this->visit('/mle-demo')
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

it('can control mms', function ($theme, $dataSource, $xhr, $storage) use ($waitTime) {

    // get the file input and the upload button (submit button)
    $inputSelector = '@media-input-alien-single-'.$storage.'-mms';
    $uploadButtonSelector = '@upload-button-alien-single-'.$storage.'-mms';
    $gridSelector = '#alien-single-'.$storage.'-mms [data-test="media-preview-grid"]';
    $firstMediaContainer = $gridSelector . ' [data-test="media-preview-container"]:first-child';
    $editButtonSelector = $firstMediaContainer . ' [data-test="media-edit-button"]';
    $setAsFirstButtonSelector = $firstMediaContainer . ' [data-test="media-set-as-first-button"]';
    $deleteButtonSelector = $firstMediaContainer . ' [data-test="media-delete-button"]';

    $page = $this->visit('/mle-demo')
        ->assertNoJavaScriptErrors()

        // Theme switching
        ->click('@btn-theme-' . $theme)
        ->assertQueryStringHas('theme', $theme)

        // Data source switching
        ->click('@btn-data-source-' . $dataSource)
        ->assertQueryStringHas('data_source', $dataSource)

        // XHR switching (NOW uses dataset properly)
        ->click($xhr ? '@btn-use-xhr-yes' : '@btn-use-xhr-no')
        ->assertQueryStringHas('use_xhr', $xhr ? '1' : '0')

        ->assertPresent($inputSelector)

        // assert that upload button is initially enabled
        ->assertButtonEnabled($uploadButtonSelector)

        // attach image file and submit and check if spinner shows and upload is successful
        ->attach($inputSelector, $this->getFixtureAsFilePath('test.jpg'))
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.upload_success'))

        // assert that upload button is disabled after upload (single media)
        ->assertButtonDisabled($uploadButtonSelector);

    // assert that the image is visible in the preview
//    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

    // assert grid is present
    $page->assertPresent($gridSelector);

    // assert grid has media container
    $page->assertPresent($firstMediaContainer);

    // check that the media item's menu has the expected buttons and state
    $page->assertButtonEnabled($editButtonSelector);
    // TODO fix fails in plain theme
    $page->assertButtonDisabled($setAsFirstButtonSelector);
    $page->assertButtonEnabled($deleteButtonSelector);

        $page->click($deleteButtonSelector)
            ->waitForText(__('medialibrary-extensions::messages.please_wait'))
            ->waitForText(__('medialibrary-extensions::messages.medium_removed'));
        // the upload button should be enabled again
        $page->assertButtonEnabled($uploadButtonSelector);

//    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

})->group('browser')
    ->with('test_matrix');
//    ->only();

it('can upload file in mms temporary (xhr)', function () use ($waitTime) {

    // TODO
    //    session persistence	❌ broken between requests
    //    frontend preview fetch	❌ uses different session
    \Illuminate\Support\Facades\Config::set('database.default', 'media_demo');

    $inputSelector = '@media-input-alien-single-temporary-mms';
    $uploadButtonSelector = '@upload-button-alien-single-temporary-mms';

    $page = $this->visit('/mle-demo?theme=bootstrap-5&use_xhr=1')
        ->assertNoJavaScriptErrors()
        ->assertPresent($inputSelector)
        ->assertButtonEnabled($uploadButtonSelector)
        ->attach($inputSelector, $this->getFixtureAsFilePath('test.jpg'))
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.upload_success'))
        ->wait(5);
})->group('browser')->only();

it('can upload files in mmm permanent (xhr)', function () use ($waitTime) {

    \Illuminate\Support\Facades\Config::set('database.default', 'media_demo');

//    $page = $this->visit('/mle-demo?theme=bootstrap-5&use_xhr=1')
//        ->assertNoJavaScriptErrors()
//        ->assertPresent($this->getMediaInput('alien-multiple-permanent-mmm'));
//
//    // Join the paths together into a single string separated by commas
//    $multipleFiles = implode(",", [
//        $this->getFixtureAsFilePath('test.jpg'),
//        $this->getFixtureAsFilePath('test2.jpg'),
//    ]);

    $singleFile = $this->getFixtureAsFilePath('test.jpg');

//    dd($multipleFiles);

//    $page->assertButtonEnabled($this->getUploadButton('alien-multiple-permanent-mmm'))
//        ->attach($this->getMediaInput('alien-multiple-permanent-mmm'), $singleFile)
////        ->attach($this->getMediaInput('alien-multiple-permanent-mmm'), $multipleFiles)
//        ->pressAndWaitFor($this->getUploadButton('alien-multiple-permanent-mmm'), $waitTime)
//        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
//        ->waitForText(__('medialibrary-extensions::messages.upload_success'));

//    $page->script('() => console.log(document.querySelector("#aliens-multiple-temporary-mmm-media-input").files[0])');
//    $page->script('() => console.log(document.querySelector("#aliens-multiple-temporary-mmm-media-input"))');
//    expect($page->script('() => document.querySelector("#aliens-multiple-temporary-mmm-media-input").files[0].name'))->toBe('test1');
//    $this->assertPreviewImageVisible($page, 'alien-multiple-permanent-mmm');
//    $page->waitForKey(); // Useful for debugging
//    $page->assertNoConsoleLogs();

    $inputSelector = '@media-input-alien-multiple-permanent-mmm';
    $uploadButtonSelector = '@upload-button-alien-multiple-permanent-mmm';

    $page = $this->visit('/mle-demo?theme=bootstrap-5&use_xhr=1')
        ->assertNoJavaScriptErrors()
        ->assertPresent($inputSelector)
        ->assertButtonEnabled($uploadButtonSelector)
        ->attach($inputSelector, $singleFile)
        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.upload_success'));

//    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

})->group('browser')->todo();

it('can upload files in mmm permanent (non-xhr)', function () use ($waitTime) {

    // TODO fix: why do i need to do this?
    \Illuminate\Support\Facades\Config::set('database.default', 'media_demo');

//    $page = $this->visit('/mle-demo?theme=bootstrap-5&use_xhr=1')
//        ->assertNoJavaScriptErrors()
////        ->assertNoConsoleLogs()
//
//        // disable xhr
//        ->click('@btn-use-xhr-no')
//        ->assertQueryStringHas('use_xhr', '0');
//
//        $page->assertPresent($this->getMediaInput('alien-multiple-permanent-mmm'));
//
//        $page->assertButtonEnabled($this->getUploadButton('alien-multiple-permanent-mmm'))
//        ->attach($this->getMediaInput('alien-multiple-permanent-mmm'), $this->getFixtureAsFilePath('test.jpg'))
//        ->attach($this->getMediaInput('alien-multiple-permanent-mmm'), $this->getFixtureAsFilePath('test2.jpg'));
//
////            ->attach($this->getMediaInput('alien-multiple-permanent-mmm'),
////                [
////                    $this->getFixtureAsFilePath('test.jpg'),
////                    $this->getFixtureAsFilePath('test2.jpg'),
////                ]
////            $this->getFixtureAsFilePath('test.jpg')
////            )
////        $page->script(<<<'JS'
////            const input = document.getElementById('alien-multiple-permanent-mmm-media-input');
////
////            const dt = new DataTransfer();
////
////            dt.items.add(new File(['a'], 'test1.jpg', { type: 'image/jpeg' }));
////            dt.items.add(new File(['b'], 'test2.jpg', { type: 'image/jpeg' }));
////
////            input.files = dt.files;
////            input.dispatchEvent(new Event('change', { bubbles: true }));
////        JS);
//
//    $page->pressAndWaitFor($this->getUploadButton('alien-multiple-permanent-mmm'), $waitTime)
//        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
//        ->waitForText(__('medialibrary-extensions::messages.upload_success'));
//
//    $this->assertPreviewImageVisible($page, 'alien-multiple-permanent-mmm');
//

    $inputSelector = '@media-input-alien-multiple-permanent-mmm';
    $uploadButtonSelector = '@upload-button-alien-multiple-permanent-mmm';

    $page = $this->visit('/mle-demo?theme=bootstrap-5&use_xhr=1')
        ->assertNoJavaScriptErrors()

        ->click('@btn-use-xhr-no')
        ->assertQueryStringHas('use_xhr', '0')

        ->assertPresent($inputSelector)

        ->assertButtonEnabled($uploadButtonSelector)
        ->attach($inputSelector, $this->getFixtureAsFilePath('test.jpg'))
//        ->attach($inputSelector, $this->getFixtureAsFilePath('test2.jpg'))

        ->pressAndWaitFor($uploadButtonSelector, $waitTime)
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.upload_success'));

//    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

})->group('browser')->todo();
