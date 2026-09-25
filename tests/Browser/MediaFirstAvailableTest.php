<?php

/** @noinspection InvalidDatasetNameCaseInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

it('can display media in media first available component', function ($theme, $dataSource, $xhr) {
    // Selectors for Media First Available
    $firstAvailableDomId = 'media-first-available-media-first-available';
    $firstAvailableId = '#' . $firstAvailableDomId;
    $placeholderSelector = $firstAvailableId . '.mle-media-placeholder';
    $viewerSelector = $firstAvailableId . ' [data-mle-media-preview-image]';
    
    // Selectors for Media Manager Single
    $mmsId = '#alien-single-permanent-mms';
    $mmsInputSelector = $mmsId . ' [data-mle-media-input]';
    $mmsUploadButtonSelector = $mmsId . ' [data-mle-media-upload-button]';

    // Modal selectors
    $modalId = '#media-first-available-mod';
    $modalSelector = $modalId . '[data-mle-media-modal]';
    $modalCloseButtonSelector = $modalSelector . ' [data-mle-modal-close]';

    $xhrInt = $xhr ? 1 : 0;
    $waitTime = $xhr ? $this->waitTimeXhr : $this->waitTimeNonXhr;

    $dataSourceResolver = app(DataSourceResolver::class);
    $resolvedConnection = $dataSourceResolver->resolveConnection($dataSource);

    $this->assertDatabaseCount('media', 0, $resolvedConnection);

    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $firstAvailableId);

    // 1. Check placeholder is visible initially
    $page->assertPresent($placeholderSelector)
        ->assertSee(__('medialibrary-extensions::messages.no_medium'));

    // 2. Upload an image via MMS
    $this->scrollIntoView($page, $mmsId);
    $page->attach($mmsInputSelector, $this->getRandomFixture())
        ->press($mmsUploadButtonSelector);
    
    if (!$xhr) {
        $page->wait($waitTime);
    }
    $page->assertSee(__('medialibrary-extensions::messages.upload_success'));
    $this->assertDatabaseCount('media', 1, $resolvedConnection);

    // 3. Refresh page to see the media in First Available component
    $page->refresh();
    $this->scrollIntoView($page, $firstAvailableId);

    // 4. Check media is now visible in First Available
    $page->assertMissing($placeholderSelector)
        ->assertPresent($viewerSelector);

    // 5. Test modal expansion
    $page->click($firstAvailableId)
        ->assertVisible($modalSelector)
        ->click($modalCloseButtonSelector)
        ->assertMissing($modalSelector);

    $page->page()->close();
})->group('browser')
    ->with([
        'bootstrap + demo default + xhr' => ['bootstrap-5', 'demo_default', true],
        'plain + demo default + xhr' => ['plain', 'demo_default', true],
    ]);

it('respects collection order priority in media first available component', function ($theme, $dataSource) {
    $firstAvailableId = '#media-first-available-media-first-available';
    $viewerSelector = $firstAvailableId . ' [data-mle-media-preview-image]';

    $dataSourceResolver = app(DataSourceResolver::class);
    $resolvedConnection = $dataSourceResolver->resolveConnection($dataSource);

    // 1. Seed media directly into the database
    $model = Alien::on($resolvedConnection)->first();
    
    // Add a document first (lower priority in demo: 'document' => 'alien-single-document')
    $model->addMedia($this->getFixtureAsFilePath('dummy.pdf'))
        ->preservingOriginal()
        ->toMediaCollection('alien-single-document');
        
    // Add an image second (higher priority in demo: 'image' => 'alien-single-image')
    $model->addMedia($this->getRandomFixture())
        ->preservingOriginal()
        ->toMediaCollection('alien-single-image');

    $this->assertDatabaseCount('media', 2, $resolvedConnection);

    // 2. Visit the page
    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $firstAvailableId);
    
    // 3. It should show the image because 'image' collection comes before 'document' in the demo configuration
    $page->assertPresent($viewerSelector);
    
    $page->page()->close();
})->group('browser')
    ->with([
        'bootstrap + demo default' => ['bootstrap-5', 'demo_default'],
        'plain + demo default' => ['plain', 'demo_default'],
    ]);
