<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerSingle;

beforeEach(function () {
    // Define fake routes used in your MediaManager components
    Route::name('mlbrgn-mle.media-upload-multiple')->post('media-upload-multiple', fn () => 'uploaded');
    Route::name('mlbrgn-mle.media-upload')->post('media-upload', fn () => 'uploaded');

    // Other test config
    Config::set('media-library-extensions.frontend_theme', 'plain');
});

it('initializes correctly with model instance', function () {

    $model = $this->getTestBlogModel();
    $component = new MediaManagerSingle(
        id: 'blog-1',
        modelOrClassName: $model,
        collections: [
            'image' => 'images',
        ],
        options: [
            'showUploadForm' => true,
            'showDestroyButton' => true,
            'showOrder' => true,
        ]
    );

    expect($component->multiple)->toBeFalse()
        ->and($component->getConfig('showUploadForm'))->toBeTrue()
        ->and($component->getConfig('showDestroyButton'))->toBeTrue()
        ->and($component->getConfig('showOrder'))->toBeFalse()// mms sets showOrder to false
        ->and($component->collections)
        ->toHaveKey('image', 'images')
        ->and($component->id)->toBe('blog-1-mms');
});

it('initializes correctly with model class name', function () {
    $component = new MediaManagerSingle(
        id: 'blog-1',
        modelOrClassName: Blog::class,
        collections: [
            'youtube' => 'videos',
        ],
        options: [
            'useXhr' => false,
        ]
    );

    expect($component->multiple)->toBeFalse()
        ->and($component->modelType)->toBe(Blog::class)
        ->and($component->temporaryUploadMode)->toBeTrue()
        ->and($component->collections)
        ->toHaveKey('youtube', 'videos')
        ->and($component->getConfig('showSetAsFirstButton'))->toBeFalse()
        ->and($component->getConfig('useXhr'))->toBeFalse();
});

it('defaults optional values when omitted', function () {
    $className = Blog::class;
    Config::set('media-library-extensions.frontend_theme', 'bootstrap-5');
    $component = new MediaManagerSingle(
        id: 'blog-1',
        modelOrClassName: $className,
        collections: [
            'image' => 'blog-images',
        ],
        options: [
            'showUploadForm' => true,
        ]
    );

    expect($component->getConfig('showUploadForm'))->toBeTrue()
        ->and($component->getConfig('showDestroyButton'))->toBeTrue()
        ->and($component->getConfig('showSetAsFirstButton'))->toBeFalse()
        ->and($component->getConfig('showMediaEditButton'))->toBeTrue()
        ->and($component->getConfig('showOrder'))->toBeFalse()
        ->and($component->getConfig('temporaryUploadMode'))->toBeTrue()
        ->and($component->getConfig('uploadFieldName'))->toBe('medium')
        ->and($component->getConfig('frontendTheme'))->toBe('bootstrap-5')
        ->and($component->multiple)->toBeFalse();
});

it('renders the correct html single (plain)', function () {
    $model = $this->getModelWithMedia([
        'image' => 2,
        'document' => '1',
        'audio' => 1,
        'video' => 1,
    ]);

    $html = Blade::render(
        '<x-mle-media-manager-single
                id="test-media-modal"
                :model-or-class-name="$modelOrClassName"
                :collections="[\'image\' => \'images\', \'documents\' => \'documents\']"
                :options="$options"
                multiple="false"
                />',
        [
            'modelOrClassName' => $model,
            'options' => [
                'frontendTheme' => 'bootstrap-5',
            ],
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('renders the correct html single (bootstrap-5, temporary upload)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);

    $html = Blade::render(
        '<x-mle-media-manager-single
                id="test-media-modal"
                :model-or-class-name="$modelOrClassName"
                :collections="[\'image\' => \'images\']"
                :options="$options"
                multiple="false"
                />',
        [
            'modelOrClassName' => $model->getMorphClass(),
            'options' => [
                'frontendTheme' => 'bootstrap-5',
            ],
        ]
    );
    expect($html)->toMatchSnapshot();
});
