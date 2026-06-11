<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\View\AnonymousComponent;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

beforeEach(function () {
    // Mock laravel-form-components
    Blade::component('form-form', AnonymousComponent::class);
    Blade::component('form-html-editor', AnonymousComponent::class);
    Blade::component('form-input', AnonymousComponent::class);
    Blade::component('form-checkbox', AnonymousComponent::class);
    Blade::component('form-select', AnonymousComponent::class);
    Blade::component('form-submit', AnonymousComponent::class);
});

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

it('can visit the demo page', function () {

    $this->visit('/mle-demo')
        ->assertNoJavaScriptErrors()
        ->assertSee('Laravel Media Library Extensions Component tests')
        ->assertSee('Media Manager Single')
        ->assertSee('Media Manager Multiple')
        ->assertSee('Media Carousel')
        ->assertSee('Media Lab')
        ->assertSee('Media First Available');
})->group('browser');

it('can switch theme, XHR and DataSource', function () {

    $this->visit('/mle-demo')
        ->assertNoJavaScriptErrors()

        // theme
        ->click('@btn-theme-plain')
        ->assertQueryStringHas('theme', 'plain')

        ->click('@btn-theme-bootstrap-5')
        ->assertQueryStringHas('theme', 'bootstrap-5')

        // DataSource
        ->click('@btn-data-source-default')
        ->assertQueryStringHas('data_source', 'default')

        ->click('@btn-data-source-demo')
        ->assertQueryStringHas('data_source', 'demo')

        // XHR
        ->click('@btn-use-xhr-no')
        ->assertQueryStringHas('use_xhr', '0')

        ->click('@btn-use-xhr-yes')
        ->assertQueryStringHas('use_xhr', '1');
});

it('can upload file in mms permanent (xhr)', function () {

    $page = $this->visit('/mle-demo?theme=bootstrap-5&use_xhr=1')
        ->assertNoJavaScriptErrors()
        ->assertPresent($this->getMediaInput('alien-single-permanent-mms'));

    $page->assertButtonEnabled($this->getUploadButton('alien-single-permanent-mms'))
        ->attach($this->getMediaInput('alien-single-permanent-mms'), $this->getFixtureAsFilePath('test.jpg'))
        ->click($this->getUploadButton('alien-single-permanent-mms'))
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.upload_success'));

    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');
//    $page->assertButtonEnabled($this->getMenuEnd('alien-single-permanent-mms') . ' ' . $this->getMenuButton('alien-single-permanent-mms'));

//    $this->assertButtonEnabled('id');
    $page->assertButtonDisabled($this->getUploadButton('alien-single-permanent-mms'));

})->group('browser');

it('can upload file in mms permanent (non-xhr)', function () {

    $page = $this->visit('/mle-demo?theme=bootstrap-5&use_xhr=1')
        ->assertNoJavaScriptErrors()

        // disable xhr
        ->click('@btn-use-xhr-no')
        ->assertQueryStringHas('use_xhr', '0')

        ->assertPresent($this->getMediaInput('alien-single-permanent-mms'))
        ->assertButtonEnabled($this->getUploadButton('alien-single-permanent-mms'))

        ->attach($this->getMediaInput('alien-single-permanent-mms'), $this->getFixtureAsFilePath('test.jpg'))

        ->click($this->getUploadButton('alien-single-permanent-mms'))
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.upload_success'));

    $this->assertPreviewImageVisible($page, 'alien-single-permanent-mms');

    $page->assertButtonDisabled($this->getUploadButton('alien-single-permanent-mms'));

})->group('browser');

it('can upload file in mms temporary (xhr)', function () {

    // TODO
    //    session persistence	❌ broken between requests
    //    frontend preview fetch	❌ uses different session
    \Illuminate\Support\Facades\Config::set('database.default', 'media_demo');


    $page = $this->visit('/mle-demo?theme=bootstrap-5&use_xhr=1')
        ->assertNoJavaScriptErrors()
        ->assertPresent($this->getMediaInput('alien-single-temporary-mms'))
        ->assertButtonEnabled($this->getUploadButton('alien-single-temporary-mms'))
        ->attach($this->getMediaInput('alien-single-temporary-mms'), $this->getFixtureAsFilePath('test.jpg'))
        ->click($this->getUploadButton('alien-single-temporary-mms'))
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.upload_success'));

    Log::info('test ' . TemporaryUpload::count());
    Log::info('test ' . print_r(Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload::latest()->first()?->toArray(), true));
//    dump(TemporaryUpload::count());
//
//    dump(
//        TemporaryUpload::latest()->first()?->toArray()
//    );

    // TODO preview image not showing
//    $this->assertPreviewImageVisible($page, 'alien-single-temporary-mms');

})->group('browser');

it('can upload file in mms temporary (non-xhr)', function () {
    \Illuminate\Support\Facades\Config::set('database.default', 'media_demo');
    $this->visit('/mle-demo?theme=bootstrap-5&use_xhr=1')
        ->assertNoJavaScriptErrors()

        // disable xhr
        ->click('@btn-use-xhr-no')
        ->assertQueryStringHas('use_xhr', '0')

        ->assertPresent($this->getMediaInput('alien-single-temporary-mms'))
        ->assertButtonEnabled($this->getUploadButton('alien-single-temporary-mms'))

        ->attach($this->getMediaInput('alien-single-temporary-mms'), $this->getFixtureAsFilePath('test.jpg'))

        ->click($this->getUploadButton('alien-single-temporary-mms'))
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.upload_success'));

    Log::info('test db: ' . json_encode([
            'connection' => DB::connection()->getName(),
            'database' => DB::connection()->getDatabaseName(),
        ]));

    Log::info('after upload check' . json_encode([
        'session' => session()->getId(),
        'db_count' => TemporaryUpload::count(),
    ]));

    // TODO preview image not showing
    //    $this->assertPreviewImageVisible($page, 'alien-single-temporary-mms');

})->group('browser');

it('can upload files in mmm permanent (xhr)', function () {

    \Illuminate\Support\Facades\Config::set('database.default', 'media_demo');

    $page = $this->visit('/mle-demo?theme=bootstrap-5&use_xhr=1')
        ->assertNoJavaScriptErrors()
        ->assertPresent($this->getMediaInput('alien-multiple-permanent-mmm'));

    // Join the paths together into a single string separated by newlines
//    $multipleFiles = implode("\n", [
//        $this->getFixtureAsFilePath('test.jpg'),
//        $this->getFixtureAsFilePath('test2.jpg'),
//    ]);

    $page->assertButtonEnabled($this->getUploadButton('alien-multiple-permanent-mmm'))
        ->attach($this->getMediaInput('alien-multiple-permanent-mmm'), $this->getFixtureAsFilePath('test.jpg'))
//        ->attach($this->getMediaInput('alien-multiple-permanent-mmm'), $multipleFiles)
        ->click($this->getUploadButton('alien-multiple-permanent-mmm'))
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.upload_success'));

    $this->assertPreviewImageVisible($page, 'alien-multiple-permanent-mmm');


})->group('browser');

it('can upload files in mmm permanent (non-xhr)', function () {

    // TODO fix: why do i need to do this?
    \Illuminate\Support\Facades\Config::set('database.default', 'media_demo');

    $page = $this->visit('/mle-demo?theme=bootstrap-5&use_xhr=1')
        ->assertNoJavaScriptErrors()
//        ->assertNoConsoleLogs()

        // disable xhr
        ->click('@btn-use-xhr-no')
        ->assertQueryStringHas('use_xhr', '0');

        $page->assertPresent($this->getMediaInput('alien-multiple-permanent-mmm'));

        $page->assertButtonEnabled($this->getUploadButton('alien-multiple-permanent-mmm'))
        ->attach($this->getMediaInput('alien-multiple-permanent-mmm'), $this->getFixtureAsFilePath('test.jpg'))
        ->attach($this->getMediaInput('alien-multiple-permanent-mmm'), $this->getFixtureAsFilePath('test2.jpg'));

//            ->attach($this->getMediaInput('alien-multiple-permanent-mmm'),
//                [
//                    $this->getFixtureAsFilePath('test.jpg'),
//                    $this->getFixtureAsFilePath('test2.jpg'),
//                ]
//            $this->getFixtureAsFilePath('test.jpg')
//            )
//        $page->script(<<<'JS'
//            const input = document.getElementById('alien-multiple-permanent-mmm-media-input');
//
//            const dt = new DataTransfer();
//
//            dt.items.add(new File(['a'], 'test1.jpg', { type: 'image/jpeg' }));
//            dt.items.add(new File(['b'], 'test2.jpg', { type: 'image/jpeg' }));
//
//            input.files = dt.files;
//            input.dispatchEvent(new Event('change', { bubbles: true }));
//        JS);

        $page->click($this->getUploadButton('alien-multiple-permanent-mmm'))
        ->waitForText(__('medialibrary-extensions::messages.please_wait'))
        ->waitForText(__('medialibrary-extensions::messages.upload_success'));

    $this->assertPreviewImageVisible($page, 'alien-multiple-permanent-mmm');

})->group('browser');
//    ->only();
