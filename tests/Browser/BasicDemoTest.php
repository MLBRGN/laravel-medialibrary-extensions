<?php

/** @noinspection InvalidDatasetNameCaseInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

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

});

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
});
