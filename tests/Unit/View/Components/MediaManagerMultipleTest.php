<?php

use Illuminate\Support\Facades\Blade;
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
        id: 'blog-1',
        modelOrClassName: $model,
        collections: ['image' => 'images'],
        options: [
            'showUploadForm' => true,
            'showDestroyButton' => true,
            'showOrder' => true,
        ],
    );

    expect($component->multiple)->toBeTrue()
        ->and($component->showUploadForm)->toBeTrue()
        ->and($component->showDestroyButton)->toBeTrue()
        ->and($component->showOrder)->toBeTrue()
        ->and($component->collections)
        ->toHaveKey('image', 'images')
        ->and($component->id)->toBe('blog-1-mmm');
});

it('initializes correctly with model class name', function () {
    $className = Blog::class;
    $component = new MediaManagerMultiple(
        id: 'blog-1',
        modelOrClassName: $className,
        collections: ['youtube' => 'videos'],
        options: [
            'showUploadForm' => true,
            'showDestroyButton' => true,
            'showOrder' => true,
            'showSetAsFirstButton' => true,
            'useXhr' => false,
        ],
    );

    expect($component->multiple)->toBeTrue()
        ->and($component->modelType)->toBe($className)
        ->and($component->collections)
        ->toHaveKey('youtube', 'videos')
        ->and($component->showSetAsFirstButton)->toBeTrue()
        ->and($component->useXhr)->toBeFalse();
});

it('defaults optional values when omitted', function () {
    $className = Blog::class;
    $component = new MediaManagerMultiple(
        id: 'blog-1',
        modelOrClassName: $className,
        collections: ['image' => 'blog-images'],
        options: [
            'frontendTheme' => 'plain',
            'showUploadForm' => true,
            'showDestroyButton' => true,
            'showOrder' => false,
            'showSetAsFirstButton' => false,
            'useXhr' => false,
        ],
    );

    expect($component->showUploadForm)->toBeTrue()
        ->and($component->showDestroyButton)->toBeTrue()
        ->and($component->showSetAsFirstButton)->toBeFalse()
        ->and($component->showOrder)->toBeFalse()
        ->and($component->uploadFieldName)->toBe('media')
        ->and($component->frontendTheme)->toBe('plain')
        ->and($component->multiple)->toBeTrue();
});

it('renders the correct html multiple (plain)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-manager-multiple id="test-media-modal" :model-or-class-name="$modelOrClassName" :collections="[\'image\' => \'images\']" :frontend-theme="$frontendTheme" multiple="true"/>',
        [
            'modelOrClassName' => $model,
            'frontendTheme' => 'plain',
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('renders the correct html multiple (bootstrap-5, temporary upload)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-manager-multiple id="test-media-modal" :model-or-class-name="$modelOrClassName" :collections="[\'image\' => \'images\']"  :frontend-theme="$frontendTheme" multiple="true" />',
        [
            'modelOrClassName' => $model->getMorphClass(),
            'frontendTheme' => 'plain',
        ]
    );
    expect($html)->toMatchSnapshot();
});
