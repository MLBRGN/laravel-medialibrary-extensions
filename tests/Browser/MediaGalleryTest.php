<?php

use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

it('can interact with the media gallery', function ($theme, $dataSource, $xhr) {
    $galleryId = '#alien-gallery-gal';
    $modalId = '#alien-gallery-mod';
    $modalSelector = $modalId . '[data-mle-media-modal]';
    
    // Using alien-multiple-permanent-mmm to upload media
    $mmmId = '#alien-multiple-permanent-mmm';
    $mmmInputSelector = $mmmId . ' [data-mle-media-input]';
    $mmmUploadButtonSelector = $mmmId . ' [data-mle-media-upload-button]';

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    $dataSourceResolver = app(DataSourceResolver::class);
    $resolvedConnection = $dataSourceResolver->resolveConnection($dataSource);

    $this->assertDatabaseCount('media', 0, $resolvedConnection);

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    // 1. Upload media (at least two items to test slide targeting)
    $this->scrollIntoView($page, $mmmId);
    $page->attach($mmmInputSelector, $this->getFixtureAsFilePath('01_100x100.jpg'))
        ->press($mmmUploadButtonSelector);
    
    if ($xhr) {
        $page->waitForText(__('medialibrary-extensions::messages.upload_success'));
    } else {
        $page->wait($waitTime);
        $this->scrollIntoView($page, $mmmId);
    }

    $page->attach($mmmInputSelector, $this->getFixtureAsFilePath('02_150x150.jpg'))
        ->press($mmmUploadButtonSelector);

    if ($xhr) {
        $page->waitForText(__('medialibrary-extensions::messages.upload_success'));
    } else {
        $page->wait($waitTime);
    }

    // 2. Refresh to see in gallery
    $page->refresh();

    $this->scrollIntoView($page, $galleryId);
    
    $page->assertPresent($galleryId)
        ->assertPresent($galleryId . ' .mle-media-gallery-item')
        ->assertPresent($galleryId . ' .mle-image-responsive');

    // 3. Test modal expansion and slide targeting
    // Click the second gallery item
    $page->click($galleryId . ' .mle-media-gallery-item:nth-child(2)')
        ->assertVisible($modalSelector)
        // Verify the second carousel item is active (slide targeting)
        ->assertPresent($modalId . ' .mle-media-carousel-item:nth-child(2).active')
        ->click($modalSelector . ' [data-mle-modal-close]')
        ->assertMissing($modalSelector);

    $page->page()->close();
})->group('browser')
    ->with('media_lab_test_matrix')
    ->flaky();
