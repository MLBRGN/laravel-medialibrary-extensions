<?php

use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

beforeEach(function () {
    View::share('errors', new ViewErrorBag());
});

it('initializes with min, required and name properties', function () {
    $model = Blog::create(['title' => 'test']);
    $component = new MediaManager(
        id: 'test-1',
        modelReference: $model,
        collections: ['image' => 'images'],
        minMediaCount: 2,
        required: true,
        name: 'gallery'
    );

    expect($component->minMediaCount)->toBe(2)
        ->and($component->required)->toBeTrue()
        ->and($component->name)->toBe('gallery')
        ->and($component->getConfig('minMediaCount'))->toBe(2)
        ->and($component->getConfig('required'))->toBeTrue()
        ->and($component->getConfig('name'))->toBe('gallery');
});

it('normalizes minMediaCount to 1 when required is true and min is 0', function () {
    $model = Blog::create(['title' => 'test']);
    $component = new MediaManager(
        id: 'test-1',
        modelReference: $model,
        collections: ['image' => 'images'],
        minMediaCount: 0,
        required: true
    );

    expect($component->minMediaCount)->toBe(1)
        ->and($component->required)->toBeTrue();
});

it('keeps minMediaCount at 0 when required is false and min is 0', function () {
    $model = Blog::create(['title' => 'test']);
    $component = new MediaManager(
        id: 'test-1',
        modelReference: $model,
        collections: ['image' => 'images'],
        minMediaCount: 0,
        required: false
    );

    expect($component->minMediaCount)->toBe(0)
        ->and($component->required)->toBeFalse();
});

it('renders hidden media count input when name is provided', function () {
    $model = Blog::create(['title' => 'test']);
    
    $html = Blade::render(<<<'BLADE'
        <x-mle-media-manager
            id="test-1"
            :model-reference="$model"
            :collections="['image' => 'images']"
            name="gallery"
        />
    BLADE, ['model' => $model]);

    expect($html)->toContain('name="gallery"');
    expect($html)->toContain('data-mle-media-count="test-1"');
    expect($html)->toContain('value="0"');
});

it('defaults name to id and renders hidden media count input when name is not provided', function () {
    $model = Blog::create(['title' => 'test']);
    
    $html = Blade::render(<<<'BLADE'
        <x-mle-media-manager
            id="test-1"
            :model-reference="$model"
            :collections="['image' => 'images']"
        />
    BLADE, ['model' => $model]);

    expect($html)->toContain('name="test-1"');
    expect($html)->toContain('data-mle-media-count="test-1"');
});

it('renders required indicator when required is true', function () {
    $model = Blog::create(['title' => 'test']);
    
    $html = Blade::render(<<<'BLADE'
        <x-mle-media-manager
            id="test-1"
            :model-reference="$model"
            :collections="['image' => 'images']"
            required
        />
    BLADE, ['model' => $model]);

    expect($html)->toContain('<span class="mle-required-indicator">*</span>');
});

it('renders required indicator when min is greater than 0', function () {
    $model = Blog::create(['title' => 'test']);
    
    $html = Blade::render(<<<'BLADE'
        <x-mle-media-manager
            id="test-1"
            :model-reference="$model"
            :collections="['image' => 'images']"
            :min-media-count="2"
        />
    BLADE, ['model' => $model]);

    expect($html)->toContain('<span class="mle-required-indicator">*</span>');
});

it('does not render required indicator when required is false and min is 0', function () {
    $model = Blog::create(['title' => 'test']);
    
    $html = Blade::render(<<<'BLADE'
        <x-mle-media-manager
            id="test-1"
            :model-reference="$model"
            :collections="['image' => 'images']"
            :required="false"
            :min="0"
        />
    BLADE, ['model' => $model]);

    expect($html)->not()->toContain('<span class="mle-required-indicator">*</span>');
});

it('renders validation errors for the given name', function () {
    $model = Blog::create(['title' => 'test']);
    
    // Simulate validation error
    $errors = new ViewErrorBag();
    $errors->put('default', new MessageBag(['gallery' => ['The gallery field is required.']]));
    View::share('errors', $errors);

    $html = Blade::render(<<<'BLADE'
        <x-mle-media-manager
            id="test-1"
            :model-reference="$model"
            :collections="['image' => 'images']"
            name="gallery"
        />
    BLADE, ['model' => $model]);

    expect($html)->toContain('<div class="mle-alert alert alert-danger mle-error-message" data-mle-error-alert>');
    expect($html)->toContain('The gallery field is required.');
    expect($html)->toContain('</div>');
});