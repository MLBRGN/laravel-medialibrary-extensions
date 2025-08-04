<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerMultiple;

beforeEach(function () {
    // Define fake routes used in your MediaManager components
    Route::name('mlbrgn-mle.media-upload-multiple')->post('media-upload-multiple', fn () => 'uploaded');
    Route::name('mlbrgn-mle.media-upload')->post('media-upload', fn () => 'uploaded');

    // Other test config
    Config::set('media-library-extensions.frontend_theme', 'plain');
});

it('initializes correctly with model instance', function () {

    $model = Blog::create(['title' => 'test']);
    $component = new MediaManagerMultiple(
        modelOrClassName: $model,
        imageCollection: 'images',
        uploadEnabled: true,
        destroyEnabled: true,
        showOrder: true,
        id: 'blog-1'
    );

    expect($component->multiple)->toBeTrue()
        ->and($component->uploadEnabled)->toBeTrue()
        ->and($component->destroyEnabled)->toBeTrue()
        ->and($component->showOrder)->toBeTrue()
        ->and($component->imageCollection)->toBe('images')
        ->and($component->id)->toBe('blog-1-media-manager-multiple');
});

it('initializes correctly with model class name', function () {
    $component = new MediaManagerMultiple(
        modelOrClassName: Blog::class,
        youtubeCollection: 'videos',
        setAsFirstEnabled: true,
        useXhr: false,
    );

    expect($component->multiple)->toBeTrue()
        ->and($component->modelType)->toBe(Blog::class)
        ->and($component->youtubeCollection)->toBe('videos')
        ->and($component->setAsFirstEnabled)->toBeTrue()
        ->and($component->useXhr)->toBeFalse();
});

it('defaults optional values when omitted', function () {
    $component = new MediaManagerMultiple(modelOrClassName: Blog::class);

    expect($component->uploadEnabled)->toBeFalse()
        ->and($component->destroyEnabled)->toBeFalse()
        ->and($component->setAsFirstEnabled)->toBeFalse()
        ->and($component->showMediaUrl)->toBeFalse()
        ->and($component->showOrder)->toBeFalse()
        ->and($component->uploadFieldName)->toBe('media')
        ->and($component->frontendTheme)->toBe('bootstrap-5')
        ->and($component->multiple)->toBeTrue();
});
