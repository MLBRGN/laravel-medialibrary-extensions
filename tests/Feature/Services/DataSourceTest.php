<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaModal;

it('DataSourceResolver throws wehn invalid dataSource provided', function () {
    Config::set('database.default', 'testbench');
    $resolver = new DataSourceResolver;

    expect(fn () => $resolver->resolveConnection('defa'))->toThrow(\InvalidArgumentException::class);
});

it('DataSourceResolver resolves demo connection when dataSource is demo', function () {
    $resolver = new DataSourceResolver;

    expect($resolver->resolveConnection('test_alt'))->toBe(PackageInfrastructure::connection('test', 'alt'));
});

it('MediaService make uses the resolved connection', function () {
    $mediaService = app(MediaService::class);
    $model = $mediaService->make(Blog::class, 'test_alt');

    expect($model->getConnectionName())->toBe(PackageInfrastructure::connection('test', 'alt'));
});

it('MediaService resolveModelById uses the resolved connection', function () {
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

    $found = $service->resolveModelById(Blog::class, $blog->id, 'demo');

    expect($found->getConnectionName())->toBe('mle_demo')// TODO don't know how to handle separate test db yet
        ->and($found->id)->toBe($blog->id);

    Schema::connection('mle_test_demo')->dropIfExists('blogs');
})->todo('refactor this test');
