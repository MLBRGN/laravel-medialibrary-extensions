<?php

/** @noinspection InvalidDatasetNameCaseInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;
use Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure;

beforeEach(function () {
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
});

it('falls back to other collections if lab collection is empty', function () {
    $dataSource = 'demo_default';
    
    // Ensure lab collection is empty
    $model = new Alien;
    $connection = app(DataSourceResolver::class)->resolveConnection($dataSource);
    $model->setConnection($connection);
    
    /** @var Alien $existingModel */
    $existingModel = $model->newQuery()->first() ?: $model->newQuery()->create();
    $existingModel->clearMediaCollection('alien-media-lab');
    $existingModel->clearMediaCollection('alien-single-image');
    
    // Add media to a different collection
    $disk = PackageInfrastructure::disk('demo');
    $demoImage = __DIR__.'/../../resources/demo/demo_small.jpeg';
    
    $existingModel->addMedia($demoImage)
        ->preservingOriginal()
        ->toMediaCollection('alien-single-image', $disk);
    
    $this->assertDatabaseHas('media', ['collection_name' => 'alien-single-image'], $connection);
    $this->assertDatabaseMissing('media', ['collection_name' => 'alien-media-lab'], $connection);

    $viewerSelector = '[id^="standalone-viewer-media-viewer"]';

    $page = $this->visit("/mle-demo?theme=bootstrap-5&data_source=$dataSource")
        ->assertNoJavaScriptErrors()
        ->assertSee("Media Viewer (Standalone)")
        ->assertDontSee('No media available for standalone viewer')
        ->assertDontSee('Media lab not showing, no media available');

    $this->scrollIntoView($page, $viewerSelector);
    $page->assertPresent($viewerSelector);
    
    $page->page()->close();
})->group('browser');
