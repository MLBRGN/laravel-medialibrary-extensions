<?php

/** @noinspection InvalidDatasetNameCaseInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

it('can display media in media responsive component and expand in modal', function ($theme, $dataSource, $xhr) {
    // Selectors for Media Responsive
    $mediaResponsiveDomId = 'media-responsive-media-responsive';
    $mediaResponsiveId = '#'.$mediaResponsiveDomId;
    $viewerSelector = $mediaResponsiveId.'[data-mle-media-preview-image]';

    // Selectors for Media Manager Single
    $mmsId = '#alien-single-permanent-mms';
    $mmsInputSelector = $mmsId.' [data-mle-media-input]';
    $mmsUploadButtonSelector = $mmsId.' [data-mle-media-upload-button]';

    // Modal selectors
    $modalBaseId = 'media-responsive-mod';
    $modalSelector = $this->getModalSelector($theme, $modalBaseId);
    $modalCloseButtonSelector = $modalSelector.' '.$this->getModalCloseSelector($theme);

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    $dataSourceResolver = app(DataSourceResolver::class);
    $resolvedConnection = $dataSourceResolver->resolveConnection($dataSource);

    $this->assertDatabaseCount('media', 0, $resolvedConnection);

    $page = $this->visit("/mlbrgn-mle/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt&section=mms-permanent,responsive")
        ->assertNoJavaScriptErrors();

    // 1. Upload an image via MMS
    $this->scrollIntoView($page, $mmsId);
    $page->attach($mmsInputSelector, $this->getRandomFixture())
        ->click($mmsUploadButtonSelector)
        ->assertSee(__('medialibrary-extensions::messages.upload_success'));
    $this->assertDatabaseCount('media', 1, $resolvedConnection);

    // 2. Refresh page to see the media in Media Responsive component
    $page->refresh();
    $this->scrollIntoView($page, $mediaResponsiveId);

    // 3. Check media is visible
    $page->assertPresent($viewerSelector);

    // 4. Test modal expansion
    $page->click($mediaResponsiveId);
    $this->waitForVisible($page, $modalSelector)
        ->click($modalCloseButtonSelector);
    $this->waitForMissing($page, $modalSelector);

    $page->page()->close();
})->group('browser')
    ->with([
        'bootstrap 5 + demo default + xhr' => ['bootstrap-5', 'demo_default', true],
        'bootstrap 6 + demo default + xhr' => ['bootstrap-6', 'demo_default', true],
        'plain + demo default + xhr' => ['plain', 'demo_default', true],
    ]);
