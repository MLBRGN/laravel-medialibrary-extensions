<?php

/** @noinspection InvalidDatasetNameCaseInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

it('can render MediaViewer standalone', function ($theme, $dataSource, $xhr) {
    // MediaViewer appends '-media-viewer' and then the sub-component appends its own suffix (e.g. '-image-responsive')
    $viewerSelector = '[id^="standalone-viewer-media-viewer"]';
    $modalSelector = '#standalone-viewer-mod[data-mle-media-modal]';
    
    $xhrInt = $xhr ? 1 : 0;
    
    $this->ensureLabMedium($dataSource);

    $dataSourceResolver = app(DataSourceResolver::class);
    $resolvedConnection = $dataSourceResolver->resolveConnection($dataSource);
    $this->assertDatabaseHas('media', ['collection_name' => 'alien-media-lab'], $resolvedConnection);
    
    $page = $this->visit("/mle-demo?theme=$theme&data_source=$dataSource&use_xhr=$xhrInt")
        ->assertNoJavaScriptErrors()
        ->assertSee("Media Viewer (Standalone)")
        ->assertDontSee('No media available');

    $this->scrollIntoView($page, $viewerSelector);

    $page->assertPresent($viewerSelector)
        ->assertVisible($viewerSelector);
        
    // Since it's an image in our test setup, verify it is indeed an image element
    // and has the expected classes from MediaViewer
    $page->assertAttributeContains($viewerSelector, 'class', 'mle-media-preview-item')
        ->assertAttributeContains($viewerSelector, 'class', 'mle-image-responsive')
        ->assertAttributeContains($viewerSelector, 'class', 'mle-cursor-zoom-in')
        
        // Test modal expansion
        ->click($viewerSelector)
        ->assertVisible($modalSelector)
        // Check that the medium is present in the modal
        ->assertPresent($modalSelector . ' [data-mle-image]')
        ->click($modalSelector . ' [data-mle-modal-close]')
        ->assertMissing($modalSelector);

    $page->page()->close();
})->group('browser')
    ->with('media_viewer_test_matrix')
    ->flaky();

it('can disable expandable-in-modal', function () {
    $viewerSelector = '[id^="standalone-viewer-media-viewer"]';
    
    $this->ensureLabMedium('demo_default');

    $page = $this->visit("/mle-demo?theme=bootstrap-5&viewer_expandable=0")
        ->assertNoJavaScriptErrors();

    $this->scrollIntoView($page, $viewerSelector);

    $page->assertPresent($viewerSelector)
        ->assertAttributeDoesntContain($viewerSelector, 'class', 'mle-cursor-zoom-in')
        ->click($viewerSelector)
        ->assertMissing('#standalone-viewer-mod');
        
    $page->page()->close();
})->group('browser');

dataset('media_viewer_test_matrix', function () {
    return [
        ['bootstrap-5', 'demo_default', true],
        ['plain', 'demo_default', true],
    ];
});
