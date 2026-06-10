<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\View\AnonymousComponent;

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
//        ->assertSee('Plain')

        ->click('@btn-theme-bootstrap-5')
        ->assertQueryStringHas('theme', 'bootstrap-5')
//        ->assertSee('Bootstrap 5')

    // DataSource
        ->click('@btn-data-source-default')
        ->assertQueryStringHas('data_source', 'default')
//        ->assertSee('Plain')

        ->click('@btn-data-source-demo')
        ->assertQueryStringHas('data_source', 'demo')
//        ->assertSee('Bootstrap 5')

    // XHR
        ->click('@btn-use-xhr-no')
        ->assertQueryStringHas('use_xhr', '0')
//        ->assertSee('Plain')

        ->click('@btn-use-xhr-yes')
        ->assertQueryStringHas('use_xhr', '1');
    //        ->assertSee('Bootstrap 5');
});

it('can upload file in mms permanent (xhr)', function () {

    $page = $this->visit('/mle-demo?theme=bootstrap-5&use_xhr=1')
        ->assertNoJavaScriptErrors()
        ->assertPresent('@media-input-alien-single-mms');

        $page->assertButtonEnabled('@upload-button-alien-single-mms')
        ->attach('@media-input-alien-single-mms', $this->getFixtureAsFilePath('test.jpg'))
        ->click('@upload-button-alien-single-mms')
        ->wait(2);

//        $firstMediumId = $page->attribute('#alien-single-mms .mle-media-preview-container:first-child', 'id');
//        dd($firstMediumId);

        $page->assertButtonEnabled('#alien-single-mms .mle-media-preview-container:first-child .mle-media-preview-menu-end button:first-child');

        $page->assertButtonDisabled('@upload-button-alien-single-mms')
        ->assertSee(__('medialibrary-extensions::messages.upload_success'));
//        ->assertSee(__('medialibrary-extensions::messages.only_one_medium_allowed'));

})->group('browser')->only();

it('can upload file in mms permanent (non-xhr)', function () {

    $page = $this->visit('/mle-demo?theme=bootstrap-5&use_xhr=1')
        ->assertNoJavaScriptErrors()

        // disable xhr
        ->click('@btn-use-xhr-no')
        ->assertQueryStringHas('use_xhr', '0')

        ->assertPresent('@media-input-alien-single-mms')
        ->assertButtonEnabled('@upload-button-alien-single-mms')

        ->attach('@media-input-alien-single-mms', $this->getFixtureAsFilePath('test.jpg'))

        ->click('@upload-button-alien-single-mms')
        ->wait(2)
        ->assertButtonDisabled('@upload-button-alien-single-mms')
        ->assertSee(__('medialibrary-extensions::messages.upload_success'));
//        ->assertSee(__('medialibrary-extensions::messages.only_one_medium_allowed'));

})->group('browser')->only();

it('can upload file in mms temporary (xhr)', function () {

    $page = $this->visit('/mle-demo?theme=bootstrap-5&use_xhr=1')
        ->assertNoJavaScriptErrors()
        ->assertPresent('@media-input-aliens-single-temporary-mms')
        ->assertButtonEnabled('@upload-button-aliens-single-temporary-mms')
        ->attach('@media-input-aliens-single-temporary-mms', $this->getFixtureAsFilePath('test.jpg'))
        ->click('@upload-button-aliens-single-temporary-mms')
        ->wait(2)
//        ->assertButtonDisabled('@upload-button-aliens-single-temporary-mms')
        ->assertSee(__('medialibrary-extensions::messages.upload_success'));
//        ->assertSee(__('medialibrary-extensions::messages.only_one_medium_allowed'));

})->group('browser')->only();

it('can upload file in mms temporary (non-xhr)', function () {

    $page = $this->visit('/mle-demo?theme=bootstrap-5&use_xhr=1')
        ->assertNoJavaScriptErrors()

        // disable xhr
        ->click('@btn-use-xhr-no')
        ->assertQueryStringHas('use_xhr', '0')

        ->assertPresent('@media-input-aliens-single-temporary-mms')
        ->assertButtonEnabled('@upload-button-aliens-single-temporary-mms')

        ->attach('@media-input-aliens-single-temporary-mms', $this->getFixtureAsFilePath('test.jpg'))

        ->click('@upload-button-aliens-single-temporary-mms')
        ->wait(2)
//        ->assertButtonDisabled('@upload-button-aliens-single-temporary-mms')
        ->assertSee(__('medialibrary-extensions::messages.upload_success'));
//        ->assertSee(__('medialibrary-extensions::messages.only_one_medium_allowed'));

})->group('browser')->only();
