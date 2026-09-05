<?php

/** @noinspection InvalidDatasetNameCaseInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

use Illuminate\Support\Facades\Config;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

it('promotes temporary uploads to permanent media on form submit', function () {
    $theme = 'bootstrap-5';
    $dataSource = 'demo_default';
    $xhrInt = 1;
    $waitTime = 0.2;

    $mmmTemporaryId = '#alien-multiple-temporary-mmm';
    $mmmTemporaryInputSelector = $mmmTemporaryId.' [data-mle-media-input]';
    $mmmTemporaryUploadButtonSelector = $mmmTemporaryId.' [data-mle-media-upload-button]';

    $mmmPermanentId = '#alien-multiple-permanent-mmm';
    $mmmPermanentGridSelector = $mmmPermanentId.' [data-mle-media-preview-grid]';

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $mmmTemporaryId);

    // 1. Upload an image to temporary MMM
    $page->attach($mmmTemporaryInputSelector, $this->getRandomFixture())
        ->pressAndWaitFor($mmmTemporaryUploadButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // 2. Submit the form to create the model and promote media
    // We need to click the specific "Save model" button for the MMM form
    $page->press($mmmTemporaryId.' ~ form button[type="submit"]')
        ->wait($waitTime)
        ->assertPathIs('/mle-demo')
        ->wait($waitTime);

    // 3. Verify it appears in the permanent MMM
    $this->scrollIntoView($page, $mmmPermanentId);
    $page->wait($waitTime)
        ->assertPresent($mmmPermanentGridSelector.' [data-mle-media-preview-container]:first-child [data-mle-media-preview-item] [data-mle-media-preview-image]');

    $page->page()->close();
})->group('browser');

it('promotes multiple temporary uploads to permanent media on form submit (MMM temporary)', function () {
    // Allow multiple items in the shared collection for this test
    Config::set('medialibrary-extensions.max_items_in_shared_media_collections', 3);
    $theme = 'bootstrap-5';
    $dataSource = 'demo_default';
    $xhrInt = 1;
    $waitTime = 0.2;

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
        ->assertSee(__('medialibrary-extensions::messages.upload_success'));

    $page->attach($mmmTemporaryInputSelector, $this->getRandomFixture())
        ->pressAndWaitFor($mmmTemporaryUploadButtonSelector, $waitTime)
        ->assertSee(__('medialibrary-extensions::messages.upload_success'));

    // 2. Submit the specific MMM form's save button to create the model and promote media
    $page->pressAndWaitFor($mmmTemporaryId.' ~ form button[type="submit"]', $waitTime)
        ->wait($waitTime)
        ->assertPathIs('/mle-demo')
        ->wait($waitTime);

    // 3. Verify at least two items appear in the permanent MMM grid
    $this->scrollIntoView($page, $mmmPermanentId);
    $page->assertPresent($mmmPermanentGridSelector);
    $page->assertPresent($mmmPermanentGridSelector.' [data-mle-media-preview-container]:first-child [data-mle-media-preview-item] [data-mle-media-preview-image]');
    $page->assertPresent($mmmPermanentGridSelector.' [data-mle-media-preview-container]:nth-child(2) [data-mle-media-preview-item] [data-mle-media-preview-image]');

    $page->page()->close();
})->group('browser');
