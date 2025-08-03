<?php

use Illuminate\Support\Facades\Config;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaCarousel;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

beforeEach(function () {
    Config::set('media-library-extensions.frontend_theme', 'bootstrap-5');
});

it('initializes correctly with a single media collection', function () {
    $model = mock(HasMedia::class);
    $mediaItems = MediaCollection::make();

    $model->shouldReceive('getMedia')
        ->once()
        ->with('images')
        ->andReturn($mediaItems);

    $component = new MediaCarousel(
        model: $model,
        mediaCollection: 'images',
        id: 'carousel-id'
    );

    expect($component->mediaItems)->toBe($mediaItems)
        ->and($component->mediaCount)->toBe(0)
        ->and($component->id)->toBe('carousel-id-carousel')
        ->and($component->frontend)->toBe('bootstrap-5');
});

it('initializes correctly with multiple media collections', function () {
    $model = mock(HasMedia::class);
    $media1 = MediaCollection::make([]);
    $media2 = MediaCollection::make([]);

    $model->shouldReceive('getMedia')
        ->once()->with('images')->andReturn($media1);
    $model->shouldReceive('getMedia')
        ->once()->with('documents')->andReturn($media2);

    $component = new MediaCarousel(
        model: $model,
        mediaCollections: ['images', 'documents'],
        id: 'carousel-multi'
    );

    expect($component->mediaItems)->toBeInstanceOf(MediaCollection::class)
        ->and($component->mediaCount)->toBe(0)
        ->and($component->id)->toBe('carousel-multi-carousel');
});

it('falls back to empty media collection when no model is provided', function () {
    $component = new MediaCarousel(
        model: null,
        id: 'carousel-empty'
    );

    expect($component->mediaItems)->toBeInstanceOf(MediaCollection::class)
        ->and($component->mediaCount)->toBe(0)
        ->and($component->id)->toBe('carousel-empty-carousel');
});

it('uses provided frontend theme if given', function () {
    $component = new MediaCarousel(
        model: null,
        frontendTheme: 'tailwind',
        id: 'custom-theme'
    );

    expect($component->frontend)->toBe('tailwind');
});
