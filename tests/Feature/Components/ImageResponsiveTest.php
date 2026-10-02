<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\View\Components\ImageResponsive;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('renders with a media object', function () {

    $medium = $this->getMedium();

    $component = new ImageResponsive(
        id: 'test-id',
        medium: $medium,
        conversion: 'thumb',
        sizes: '100vw',
        lazy: true,
        alt: 'Test image'
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
    expect($component->hasGeneratedConversion())->toBeTrue();
    expect($component->getUseConversion())->toBe('thumb');
});

it('falls back to alternative conversion when primary is not available', function () {
    $medium = $this->getMedium();
    $medium->generated_conversions = [
        'primary' => false,
        'fallback' => true,
    ];

    $component = new ImageResponsive(
        id: 'test-id',
        medium: $medium,
        conversion: 'primary',
        conversions: ['fallback'],
        sizes: '100vw',
        lazy: true,
        alt: 'Test image'
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
    expect($component->hasGeneratedConversion())->toBeTrue();
    expect($component->getUseConversion())->toBe('fallback');
});

it('uses original image when no conversions are available', function () {
    $medium = $this->getMedium();
    $medium->generated_conversions = [
        'unavailable' => false,
        'also-unavailable' => false,
    ];

    $component = new ImageResponsive(
        id: 'test-id',
        medium: $medium,
        conversion: 'unavailable',
        conversions: ['also-unavailable'],
        sizes: '100vw'
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
    expect($component->hasGeneratedConversion())->toBeFalse();
    expect($component->getUseConversion())->toBe('');
});

it('handles null media gracefully', function () {

    $component = new ImageResponsive(
        id: 'test-id',
        medium: null,
        sizes: '100vw'
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
    expect($component->hasGeneratedConversion())->toBeFalse();
    expect($component->getUseConversion())->toBe('');
});

it('handles exceptions when getting media URL', function () {
    $medium = $this->getMedium();
    $medium->generated_conversions = ['thumb' => true];

    $component = new ImageResponsive(
        id: 'test-id',
        medium: $medium,
        conversion: 'thumb',
        sizes: '100vw'
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
    expect($component->hasGeneratedConversion())->toBeTrue();
});

it('can be initialized with modal properties', function () {
    $medium = $this->getMedium();
    $model = new Alien;

    $component = new ImageResponsive(
        id: 'test-id',
        medium: $medium,
        expandableInModal: true,
        modelReference: $model,
        collections: ['images'],
        dataSource: 'default'
    );

    expect($component->expandableInModal)->toBeTrue();
    expect($component->modelReference)->toBe($model);
    expect($component->collections)->toBe(['images']);
    expect($component->dataSource)->toBe('default');

    $html = Blade::render(
        '<x-mle-image-responsive :id="$id" :medium="$medium" :expandable-in-modal="true" :model-reference="$model" :collections="[\'images\']" data-source="default" />',
        ['id' => 'test-id', 'medium' => $medium, 'model' => $model]
    );

    expect($html)->toContain('data-bs-toggle="modal"');
    expect($html)->toContain('data-bs-target="#test-id-mod"');
    expect($html)->toContain('mle-media-modal');
    expect($html)->toContain('mle-component');
    expect($html)->toContain('mle-theme-bootstrap-5');
});

it('automatically resolves modelReference from medium if not provided', function () {
    $medium = $this->getMedium();
    // In getMedium(), it is attached to $this->testModel (Blog)
    $expectedModel = $medium->model;

    $component = new ImageResponsive(
        id: 'test-id',
        medium: $medium,
        expandableInModal: true,
        // modelReference is omitted
    );

    expect($component->modelReference)->not->toBeNull();
    expect($component->modelReference->is($expectedModel))->toBeTrue();
});

it('automatically resolves modelReference for TemporaryUpload', function () {
    $tempUpload = new TemporaryUpload;

    $component = new ImageResponsive(
        id: 'test-id',
        medium: $tempUpload,
        expandableInModal: true,
    );

    expect($component->modelReference)->toBe(TemporaryUpload::class);
});

it('does not crash when medium is null and expandableInModal is true', function () {
    $component = new ImageResponsive(
        id: 'test-id',
        medium: null,
        expandableInModal: true,
    );

    expect($component->expandableInModal)->toBeFalse();

    $html = Blade::render(
        '<x-mle-image-responsive id="test-id" :medium="null" :expandable-in-modal="true" />'
    );

    expect($html)->not->toContain('data-bs-toggle="modal"');
    expect($html)->not->toContain('mle-media-modal');
});

it('uses provided placeholder attribute', function () {
    $component = new ImageResponsive(
        id: 'test-id',
        medium: null,
        placeholder: 'https://example.com/placeholder.jpg'
    );

    $view = $component->render();
    expect($view->placeholder)->toBe('https://example.com/placeholder.jpg');

    $html = Blade::render(
        '<x-mle-image-responsive id="test-id" :medium="null" placeholder="https://example.com/attr-placeholder.jpg" />'
    );
    expect($html)->toContain('src="https://example.com/attr-placeholder.jpg"');
});

it('uses model fallback when medium is null', function () {
    $model = Mockery::mock(HasMedia::class);
    $model->shouldReceive('getFirstMedia')->andReturn(null);
    $model->shouldReceive('registerMediaCollections');
    $model->shouldReceive('getFallbackMediaUrl')->with('custom-collection', '')->andReturn('https://example.com/fallback-custom-collection.jpg');

    $component = new ImageResponsive(
        id: 'test-id',
        medium: null,
        modelReference: $model,
        collections: ['custom-collection']
    );

    $view = $component->render();
    expect($view->placeholder)->toBe('https://example.com/fallback-custom-collection.jpg');
});

it('resolves medium from modelReference and collections if not provided', function () {
    $model = Mockery::mock(HasMedia::class);
    $model->shouldReceive('registerMediaCollections');
    $model->shouldReceive('getFallbackMediaUrl')->andReturn('');
    $media = Mockery::mock(Media::class)->makePartial();
    $media->generated_conversions = ['thumb' => true];
    $media->shouldReceive('getUrl')->with('thumb')->andReturn('https://example.com/media-thumb.jpg');
    $media->shouldReceive('getSrcset')->with('thumb')->andReturn('https://example.com/media-thumb.jpg 1x');

    $model->shouldReceive('getFirstMedia')->with('images')->andReturn($media);

    $component = new ImageResponsive(
        id: 'test-id',
        medium: null,
        modelReference: $model,
        collections: ['images'],
        conversion: 'thumb'
    );

    $view = $component->render();
    expect($view->url)->toContain('https://example.com/media-thumb.jpg');
    expect($view->srcset)->toBe('https://example.com/media-thumb.jpg 1x');
});

it('uses real model fallback when medium is null', function () {
    $model = new Alien;
    // We don't need to actually attach media, just test that the method is called.

    // Test default fallback
    $component = new ImageResponsive(
        id: 'test-id',
        medium: null,
        modelReference: $model,
        collections: ['alien-single-image']
    );

    $view = $component->render();
    expect($view->placeholder)->toBe('/images/alien-fallback.jpg');

    // Test conversion fallback
    $componentThumb = new ImageResponsive(
        id: 'test-id-thumb',
        medium: null,
        modelReference: $model,
        collections: ['alien-single-image'],
        conversion: 'thumb'
    );
    $viewThumb = $componentThumb->render();
    expect($viewThumb->placeholder)->toBe('/images/alien-fallback-thumb.jpg');
});

it('uses config fallback when no other options are available', function () {
    config(['medialibrary-extensions.placeholder_url' => 'https://example.com/config-placeholder.jpg']);

    $component = new ImageResponsive(
        id: 'test-id',
        medium: null,
    );

    $view = $component->render();
    expect($view->placeholder)->toBe('https://example.com/config-placeholder.jpg');
});

it('renders nothing when all fallbacks are null', function () {
    config(['medialibrary-extensions.placeholder_url' => null]);

    $component = new ImageResponsive(
        id: 'test-id',
        medium: null,
    );

    $view = $component->render();
    expect($view->placeholder)->toBeNull();

    $html = Blade::render(
        '<x-mle-image-responsive id="test-id" :medium="null" />'
    );

    // The container div should be there but empty of img tag
    expect($html)->toContain('mle-image-responsive');
    expect($html)->not->toContain('<img');
    // Specifically verify it doesn't contain the no-media-icon (SVG)
    expect($html)->not->toContain('<svg');
});

it('uses media URL and ignores placeholder when media is present', function () {
    $medium = $this->getMedium();
    $component = new ImageResponsive(
        id: 'test-id',
        medium: $medium,
        placeholder: 'https://example.com/placeholder.jpg'
    );

    $view = $component->render();
    expect($view->url)->not->toBeEmpty();
    expect($view->placeholder)->toBe('https://example.com/placeholder.jpg');

    $html = Blade::render(
        '<x-mle-image-responsive id="test-id" :medium="$medium" placeholder="https://example.com/placeholder.jpg" />',
        ['medium' => $medium]
    );

    expect($html)->toContain('test.jpg');
    expect($html)->not->toContain('src="https://example.com/placeholder.jpg"');
});

it('skips model fallback when modelReference is a class name', function () {
    $component = new ImageResponsive(
        id: 'test-id',
        medium: null,
        modelReference: TemporaryUpload::class,
    );

    $view = $component->render();
    // It should skip the model fallback because modelReference is a string, not an object implementing HasMedia
    expect($view->placeholder)->toBe(config('medialibrary-extensions.placeholder_url'));
});
