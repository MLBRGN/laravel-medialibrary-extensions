<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaCarousel;

beforeEach(function () {
    Config::set('media-library-extensions.frontend_theme', 'bootstrap-5');
});

it('initializes correctly with a single media collection', function () {
    $model = $this->getModelWithMedia(['image' => 1]);

    $component = new MediaCarousel(
        id: 'carousel-id',
        modelOrClassName: $model,
        collections: ['image_collection']
    );
    expect($component->mediaCount)->toBe(1)
        ->and($component->id)->toBe('carousel-id-crs')
        ->and($component->getConfig('frontendTheme'))->toBe('bootstrap-5');
});

it('initializes correctly with multiple media collections', function () {
    $model = $this->getModelWithMedia(['image' => 1, 'audio' => '2']);

    $component = new MediaCarousel(
        id: 'carousel-id',
        modelOrClassName: $model,
        collections: ['image_collection', 'audio_collection'],
        options: [
            'frontendTheme' => 'plain',
        ]
    );
    expect($component->mediaCount)->toBe(3)
        ->and($component->id)->toBe('carousel-id-crs')
        ->and($component->getConfig('frontendTheme'))->toBe('plain');
});

it('falls back to empty media collection when no model is provided', function () {
    $model = $this->getTestBlogModel();

    $component = new MediaCarousel(
        modelOrClassName: $model,
        id: 'carousel-empty'
    );

    expect($component->media)->toBeInstanceOf(Collection::class)
        ->and($component->mediaCount)->toBe(0)
        ->and($component->id)->toBe('carousel-empty-crs');
});

it('uses provided frontend theme if given', function () {
    $model = $this->getTestBlogModel();
    $component = new MediaCarousel(
        id: 'custom-theme',
        modelOrClassName: $model,
        options: [
            'frontendTheme' => 'tailwind',
        ]
    );

    expect($component->getConfig('frontendTheme'))->toBe('tailwind');
});

it('renders view and matches snapshot', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);
    $collections = ['images', 'documents'];

    $html = Blade::render('<x-mle-media-carousel
                    :model-or-class-name="$modelOrClassName"
                    id="media-carousel"
                    :collections="$collections"
                />', [
        'modelOrClassName' => $model,
        'collections' => $collections,
    ]);

    expect($html)->toMatchSnapshot();
});
