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
    $connection = PackageInfrastructure::connection('test', 'alt');
    // Ensure idempotent setup across repeated runs
    Schema::connection($connection)->dropIfExists('blogs');
    Schema::connection($connection)->create('blogs', function ($table) {
        $table->id();
        $table->string('title');
        $table->timestamps();
    });

    // We create a model on the media_demo connection
    $blog = new Blog;
    $blog->setConnection($connection);
    $blog->title = 'Demo Blog';
    $blog->save();

    // Use a valid, explicit data source key understood by the resolver
    $found = $service->resolveModelById(Blog::class, $blog->id, 'test_alt');

    expect($found->getConnectionName())->toBe($connection)
        ->and($found->id)->toBe($blog->id);

    Schema::connection($connection)->dropIfExists('blogs');
});
