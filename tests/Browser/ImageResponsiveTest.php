<?php

/** @noinspection InvalidDatasetNameCaseInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

it('can display media in image responsive component and expand in modal', function ($theme, $dataSource, $xhr) {
    // Selectors for Image Responsive
    $imageResponsiveDomId = 'image-responsive-image-responsive';
    $imageResponsiveId = '#' . $imageResponsiveDomId;
    $viewerSelector = $imageResponsiveId . '[data-mle-media-preview-image]';
    
    // Selectors for Media Manager Single
    $mmsId = '#alien-single-permanent-mms';
    $mmsInputSelector = $mmsId . ' [data-mle-media-input]';
    $mmsUploadButtonSelector = $mmsId . ' [data-mle-media-upload-button]';

    // Modal selectors
    $modalId = '#image-responsive-mod';
    $modalSelector = $modalId . '[data-mle-media-modal]';
    $modalCloseButtonSelector = $modalSelector . ' [data-mle-modal-close]';

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    $dataSourceResolver = app(DataSourceResolver::class);
    $resolvedConnection = $dataSourceResolver->resolveConnection($dataSource);

    $this->assertDatabaseCount('media', 0, $resolvedConnection);

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    // 1. Upload an image via MMS
    $this->scrollIntoView($page, $mmsId);
    $page->attach($mmsInputSelector, $this->getRandomFixture())
        ->press($mmsUploadButtonSelector);
    
    if (!$xhr) {
        $page->wait($waitTime);
    }
    $page->assertSee(__('medialibrary-extensions::messages.upload_success'));
    $this->assertDatabaseCount('media', 1, $resolvedConnection);

    // 2. Refresh page to see the media in Image Responsive component
    $page->refresh();
    $this->scrollIntoView($page, $imageResponsiveId);

    // 3. Check media is visible
    $page->assertPresent($viewerSelector);

    // 4. Test modal expansion
    $page->click($imageResponsiveId)
        ->assertVisible($modalSelector)
        ->click($modalCloseButtonSelector)
        ->assertMissing($modalSelector);

    $page->page()->close();
})->group('browser')
    ->with([
        'bootstrap + demo default + xhr' => ['bootstrap-5', 'demo_default', true],
    ]);
