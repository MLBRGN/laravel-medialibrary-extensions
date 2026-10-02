<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaResponsive;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('renders with a media object', function () {

    $medium = $this->getMedium();

    $component = new MediaResponsive(
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

    $component = new MediaResponsive(
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

    $component = new MediaResponsive(
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

    $component = new MediaResponsive(
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

    $component = new MediaResponsive(
        id: 'test-id',
        medium: $medium,
        conversion: 'thumb',
        sizes: '100vw'
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
    expect($component->hasGeneratedConversion())->toBeTrue();
});

it('can be initialized with modal properties derived from medium', function () {
    $medium = $this->getMedium();
    $expectedModel = $medium->model;

    $component = new MediaResponsive(
        id: 'test-id',
        medium: $medium,
        expandableInModal: true,
        dataSource: 'default'
    );

    expect($component->expandableInModal)->toBeTrue();
    expect($component->modelReference->is($expectedModel))->toBeTrue();
    expect($component->collections)->toBe(['image_collection']);
    expect($component->dataSource)->toBe('default');

    $html = Blade::render(
        '<x-mle-media-responsive :id="$id" :medium="$medium" :expandable-in-modal="true" data-source="default" />',
        ['id' => 'test-id', 'medium' => $medium]
    );

    expect($html)->toContain('data-bs-toggle="modal"');
    expect($html)->toContain('data-bs-target="#test-id-mod"');
    expect($html)->toContain('mle-media-modal');
    expect($html)->toContain('mle-component');
    expect($html)->toContain('mle-theme-bootstrap-5');
});

it('automatically resolves modelReference for TemporaryUpload', function () {
    $tempUpload = $this->createTemporaryUpload();

    $component = new MediaResponsive(
        id: 'test-id',
        medium: $tempUpload,
        expandableInModal: true,
    );

    expect($component->modelReference)->toBe(TemporaryUpload::class);
});

it('does not crash when medium is null and expandableInModal is true', function () {
    $component = new MediaResponsive(
        id: 'test-id',
        medium: null,
        expandableInModal: true,
    );

    expect($component->expandableInModal)->toBeFalse();

    $html = Blade::render(
        '<x-mle-media-responsive id="test-id" :medium="null" :expandable-in-modal="true" />'
    );

    expect($html)->not->toContain('data-bs-toggle="modal"');
    expect($html)->not->toContain('mle-media-modal');
});

it('uses provided placeholder attribute', function () {
    $component = new MediaResponsive(
        id: 'test-id',
        medium: null,
        placeholder: 'https://example.com/placeholder.jpg'
    );

    $view = $component->render();
    expect($view->placeholder)->toBe('https://example.com/placeholder.jpg');

    $html = Blade::render(
        '<x-mle-media-responsive id="test-id" :medium="null" placeholder="https://example.com/attr-placeholder.jpg" />'
    );
    expect($html)->toContain('src="https://example.com/attr-placeholder.jpg"');
});

it('uses config fallback when no other options are available', function () {
    config(['medialibrary-extensions.placeholder_url' => 'https://example.com/config-placeholder.jpg']);

    $component = new MediaResponsive(
        id: 'test-id',
        medium: null,
    );

    $view = $component->render();
    expect($view->placeholder)->toBe('https://example.com/config-placeholder.jpg');
});

it('renders nothing when all fallbacks are null', function () {
    config(['medialibrary-extensions.placeholder_url' => null]);

    $component = new MediaResponsive(
        id: 'test-id',
        medium: null,
    );

    $view = $component->render();
    expect($view->placeholder)->toBeNull();

    $html = Blade::render(
        '<x-mle-media-responsive id="test-id" :medium="null" />'
    );

    // The container div should be there but empty of img tag
    expect($html)->toContain('mle-media-responsive');
    expect($html)->not->toContain('<img');
    // Specifically verify it doesn't contain the no-media-icon (SVG)
    expect($html)->not->toContain('<svg');
});

it('uses media URL and ignores placeholder when media is present', function () {
    $medium = $this->getMedium();
    $component = new MediaResponsive(
        id: 'test-id',
        medium: $medium,
        placeholder: 'https://example.com/placeholder.jpg'
    );

    $view = $component->render();
    expect($view->url)->not->toBeEmpty();
    expect($view->placeholder)->toBe('https://example.com/placeholder.jpg');

    $html = Blade::render(
        '<x-mle-media-responsive id="test-id" :medium="$medium" placeholder="https://example.com/placeholder.jpg" />',
        ['medium' => $medium]
    );

    expect($html)->toContain('test.jpg');
    expect($html)->not->toContain('src="https://example.com/placeholder.jpg"');
});
