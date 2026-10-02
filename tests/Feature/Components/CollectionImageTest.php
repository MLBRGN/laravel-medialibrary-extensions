<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
use Mlbrgn\MediaLibraryExtensions\View\Components\CollectionImage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('renders with a model and collection', function () {
    $model = new Alien;
    $media = Mockery::mock(Media::class)->makePartial();
    $media->generated_conversions = ['thumb' => true];
    $media->shouldReceive('getUrl')->with('thumb')->andReturn('https://example.com/media-thumb.jpg');
    $media->shouldReceive('getSrcset')->with('thumb')->andReturn('https://example.com/media-thumb.jpg 1x');

    $modelMock = Mockery::mock(Alien::class)->makePartial();
    $modelMock->shouldReceive('getFirstMedia')->with('images')->andReturn($media);

    $component = new CollectionImage(
        id: 'test-id',
        modelReference: $modelMock,
        collection: 'images',
        conversion: 'thumb',
        sizes: '100vw',
        lazy: true,
        alt: 'Test image'
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
    expect($component->hasGeneratedConversion())->toBeTrue();
    expect($component->getUseConversion())->toBe('thumb');
    expect($view->url)->toContain('https://example.com/media-thumb.jpg');
    expect($view->srcset)->toBe('https://example.com/media-thumb.jpg 1x');
});

it('uses model fallback when no media exists in collection', function () {
    $model = new Alien;

    $component = new CollectionImage(
        id: 'test-id',
        modelReference: $model,
        collection: 'alien-single-image'
    );

    $view = $component->render();
    expect($view->placeholder)->toBe('/images/alien-fallback.jpg');
});

it('uses provided placeholder attribute', function () {
    $model = new Alien;
    $component = new CollectionImage(
        id: 'test-id',
        modelReference: $model,
        collection: 'non-existent',
        placeholder: 'https://example.com/placeholder.jpg'
    );

    $view = $component->render();
    expect($view->placeholder)->toBe('https://example.com/placeholder.jpg');
});

it('uses config fallback when no other options are available', function () {
    config(['medialibrary-extensions.placeholder_url' => 'https://example.com/config-placeholder.jpg']);
    $model = new Alien;

    $component = new CollectionImage(
        id: 'test-id',
        modelReference: $model,
        collection: 'non-existent',
    );

    $view = $component->render();
    expect($view->placeholder)->toBe('https://example.com/config-placeholder.jpg');
});

it('can be initialized with modal properties', function () {
    $model = new Alien;
    $media = Mockery::mock(Media::class)->makePartial();
    $media->shouldReceive('getUrl')->andReturn('https://example.com/test.jpg');
    $media->shouldReceive('getSrcset')->andReturn('');

    $modelMock = Mockery::mock(Alien::class)->makePartial();
    $modelMock->shouldReceive('getFirstMedia')->with('images')->andReturn($media);

    $component = new CollectionImage(
        id: 'test-id',
        modelReference: $modelMock,
        collection: 'images',
        expandableInModal: true,
        dataSource: 'default'
    );

    expect($component->expandableInModal)->toBeTrue();
    expect($component->modelReference)->toBe($modelMock);
    expect($component->collection)->toBe('images');
    expect($component->dataSource)->toBe('default');

    $html = Blade::render(
        '<x-mle-collection-image :id="$id" :model-reference="$model" collection="images" :expandable-in-modal="true" data-source="default" />',
        ['id' => 'test-id', 'model' => $modelMock]
    );

    expect($html)->toContain('data-bs-toggle="modal"');
    expect($html)->toContain('data-bs-target="#test-id-mod"');
    expect($html)->toContain('mle-media-modal');
    expect($html)->toContain('mle-collection-image');
});

it('handles invalid modelReference gracefully', function () {
    $component = new CollectionImage(
        id: 'test-id',
        modelReference: 'invalid',
        collection: 'images'
    );

    $view = $component->render();
    expect($view->url)->toBeEmpty();
});
