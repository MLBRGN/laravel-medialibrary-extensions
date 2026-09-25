<?php

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure;
use Mlbrgn\MediaLibraryExtensions\View\Components\ImageResponsive;

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

    $component = new ImageResponsive(
        id: 'test-id',
        medium: $medium,
        expandableInModal: true,
    );

    expect($component->expandableInModal)->toBeTrue();
    // modelReference is auto-resolved from medium, which in getMedium() is attached to $this->testModel (Blog)
    $reflection = new ReflectionProperty($component, 'modelReference');
    $reflection->setAccessible(true);
    expect($reflection->getValue($component)->is($medium->model))->toBeTrue();

    $html = \Illuminate\Support\Facades\Blade::render(
        '<x-mle-image-responsive :id="$id" :medium="$medium" :expandable-in-modal="true" />',
        ['id' => 'test-id', 'medium' => $medium]
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

    $reflection = new ReflectionProperty($component, 'modelReference');
    $reflection->setAccessible(true);
    $modelReference = $reflection->getValue($component);
    
    expect($modelReference)->not->toBeNull();
    expect($modelReference->is($expectedModel))->toBeTrue();
});

it('automatically resolves modelReference for TemporaryUpload', function () {
    $tempUpload = new \Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload();
    
    $component = new ImageResponsive(
        id: 'test-id',
        medium: $tempUpload,
        expandableInModal: true,
    );

    $reflection = new ReflectionProperty($component, 'modelReference');
    $reflection->setAccessible(true);
    expect($reflection->getValue($component))->toBe(\Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload::class);
});

it('uses explicit placeholder when medium is null', function () {
    PackageInfrastructure::register('demo');
    $model = new \Mlbrgn\MediaLibraryExtensions\Models\demo\Alien();

    // alien-empty-collection has a fallback configured in Alien model
    $expectedFallbackUrl = $model->getFirstMediaUrl('alien-empty-collection');

    $component = new ImageResponsive(
        id: 'test-id',
        medium: null,
        placeholder: $expectedFallbackUrl
    );

    $component->render();
    expect($component->placeholder)->toBe($expectedFallbackUrl);
});

it('does not crash when legacy array attributes are passed', function () {
    $medium = $this->getMedium();

    $html = \Illuminate\Support\Facades\Blade::render(
        '<x-mle-image-responsive 
            :id="$id" 
            :medium="$medium" 
            :collections="[\'legacy\', \'array\']" 
            :data-source="\'legacy-string\'"
            :model-reference="$model"
        />',
        [
            'id' => 'test-id', 
            'medium' => $medium,
            'model' => $medium->model
        ]
    );

    expect($html)->toContain('mle-image-responsive');
    expect($html)->not->toContain('collections="');
});
