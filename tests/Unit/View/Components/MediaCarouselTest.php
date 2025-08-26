<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaCarousel;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

beforeEach(function () {
    Config::set('media-library-extensions.frontend_theme', 'bootstrap-5');
});

it('initializes correctly with a single media collection', function () {
    $model = $this->getTestBlogModel();
    $mediaItems = MediaCollection::make();

    $model->shouldReceive('getMedia')
        ->once()
        ->with('images')
        ->andReturn($mediaItems);

    $component = new MediaCarousel(
        modelOrClassName: $model,
        mediaCollection: 'images',
        id: 'carousel-id'
    );

    expect($component->mediaItems)->toBe($mediaItems)
        ->and($component->mediaCount)->toBe(0)
        ->and($component->id)->toBe('carousel-id-carousel')
        ->and($component->frontend)->toBe('bootstrap-5');
})->todo();

it('initializes correctly with multiple media collections', function () {
    $model = $this->getTestBlogModel();
    $media1 = MediaCollection::make([]);
    $media2 = MediaCollection::make([]);

    $model->shouldReceive('getMedia')
        ->once()->with('images')->andReturn($media1);
    $model->shouldReceive('getMedia')
        ->once()->with('documents')->andReturn($media2);

    $component = new MediaCarousel(
        modelOrClassName: $model,
        mediaCollections: ['images', 'documents'],
        id: 'carousel-multi'
    );

    expect($component->mediaItems)->toBeInstanceOf(MediaCollection::class)
        ->and($component->mediaCount)->toBe(0)
        ->and($component->id)->toBe('carousel-multi-carousel');
})->todo();

it('falls back to empty media collection when no model is provided', function () {
    $model = $this->getTestBlogModel();

    $component = new MediaCarousel(
        modelOrClassName: $model,
        id: 'carousel-empty'
    );

    expect($component->mediaItems)->toBeInstanceOf(MediaCollection::class)
        ->and($component->mediaCount)->toBe(0)
        ->and($component->id)->toBe('carousel-empty-crs');
});

it('uses provided frontend theme if given', function () {
    $model = $this->getTestBlogModel();
    $component = new MediaCarousel(
        modelOrClassName: $model,
        frontendTheme: 'tailwind',
        id: 'custom-theme'
    );

    expect($component->frontendTheme)->toBe('tailwind');
});

it('renders view and matches snapshot', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);
    $mediaCollections = ['images', 'documents'];

    $html = Blade::render('<x-mle-media-carousel
                    :model-or-class-name="$modelOrClassName"
                    id="media-carousel"
                    :media-collections="$mediaCollections"
                />', [
        'modelOrClassName' => $model,
        'mediaCollections' => $mediaCollections,
    ]);

    expect($html)->toMatchSnapshot();
});

