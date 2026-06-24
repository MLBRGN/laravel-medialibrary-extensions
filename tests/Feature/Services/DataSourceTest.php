<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

it('DataSourceResolver resolves default connection when dataSource is null', function () {
    Config::set('database.default', 'testbench');
    $resolver = new DataSourceResolver;

    expect($resolver->resolveConnection(null))->toBe('testbench');
});

it('DataSourceResolver resolves demo connection when dataSource is demo', function () {
    Config::set('medialibrary-extensions.data_sources.demo.connection', 'media_demo');
    Config::set('database.default', 'testbench'); // should NOT be this

    $resolver = new DataSourceResolver;

    expect($resolver->resolveConnection('demo'))->toBe('media_demo');
});

it('MediaService make uses the resolved connection', function () {
    Config::set('medialibrary-extensions.data_sources.demo.connection', 'media_demo');

    $mediaService = app(MediaService::class);
    $model = $mediaService->make(Blog::class, 'demo');

    expect($model->getConnectionName())->toBe('media_demo');
});

it('MediaService findMediaModel uses the resolved connection', function () {
    Config::set('medialibrary-extensions.data_sources.demo.connection', 'mle_test_demo');

    $service = app(MediaService::class);

    // We create the blogs table on the media_demo connection manually for this test
    Schema::connection('mle_test_demo')->create('blogs', function ($table) {
        $table->id();
        $table->string('title');
        $table->timestamps();
    });

    // We create a model on the media_demo connection
    $blog = new Blog;
    $blog->setConnection('mle_test_demo');
    $blog->title = 'Demo Blog';
    $blog->save();

    $found = $service->findMediaModel(Blog::class, $blog->id, 'demo');

    expect($found->getConnectionName())->toBe('mle_test_demo')
        ->and($found->id)->toBe($blog->id);

    Schema::connection('mle_test_demo')->dropIfExists('blogs');
});
