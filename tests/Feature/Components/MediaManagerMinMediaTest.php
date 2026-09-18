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
        options: [
            'minMediaCount' => 2,
        ],
        multiple: true,
        required: true,
        name: 'gallery'
    );

    expect($component->getConfig('minMediaCount'))->toBe(2)
        ->and($component->getConfig('required'))->toBeTrue()
        ->and($component->getConfig('name'))->toBe('gallery');
});

it('normalizes minMediaCount to 1 when required is true and min is 0', function () {
    $model = Blog::create(['title' => 'test']);
    $component = new MediaManager(
        id: 'test-1',
        modelReference: $model,
        collections: ['image' => 'images'],
        options: [
            'minMediaCount' => 0,
        ],
        required: true
    );

    expect($component->getConfig('minMediaCount'))->toBe(1)
        ->and($component->getConfig('required'))->toBeTrue();
});

it('keeps minMediaCount at 0 when required is false and min is 0', function () {
    $model = Blog::create(['title' => 'test']);
    $component = new MediaManager(
        id: 'test-1',
        modelReference: $model,
        collections: ['image' => 'images'],
        options: [
            'minMediaCount' => 0,
        ],
        required: false
    );

    expect($component->getConfig('minMediaCount'))->toBe(0)
        ->and($component->getConfig('required'))->toBeFalse();
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

it('does not render required indicator next to media counts', function () {
    $model = Blog::create(['title' => 'test']);
    
    $html = Blade::render(<<<'BLADE'
        <x-mle-media-manager
            id="test-1"
            :model-reference="$model"
            :collections="['image' => 'images']"
            required
        />
    BLADE, ['model' => $model]);

    // Should NOT be inside the counts span
    $countsSpan = preg_match('/<span[^>]*data-mle-media-manager-media-counts[^>]*>(.*?)<\/span>/s', $html, $matches) ? $matches[1] : '';
    expect($countsSpan)->not()->toContain('mle-required-indicator');
    
    // BUT should be in the label
    expect($html)->toContain('<label for="test-1-media-input"');
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
            :options="['minMediaCount' => 0]"
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

it('renders required indicator in upload form label', function () {
    $model = Blog::create(['title' => 'test']);
    
    $html = Blade::render(<<<'BLADE'
        <x-mle-media-manager
            id="test-1"
            :model-reference="$model"
            :collections="['image' => 'images']"
            required
        />
    BLADE, ['model' => $model]);

    // Check for indicator near the upload label
    expect($html)->toContain('<label for="test-1-media-input"');
    expect($html)->toContain('class="mle-label');
    expect($html)->toContain('<span class="mle-required-indicator">*</span>');
    // It should be inside the label based on my change
    expect($html)->toMatch('/<label for="test-1-media-input"[^>]*>.*?<span class="mle-required-indicator">\*<\/span>.*?<\/label>/s');
});

it('includes requirement text in supported files summary', function () {
    $model = Blog::create(['title' => 'test']);
    
    // Case 1: required (min=1)
    $html = Blade::render(<<<'BLADE'
        <x-mle-media-manager
            id="test-1"
            :model-reference="$model"
            :collections="['image' => 'images']"
            required
        />
    BLADE, ['model' => $model]);

    expect($html)->toContain('One medium required');
    expect($html)->not()->toContain('At least one medium is required');
    expect($html)->not()->toContain('At least one medium is required.');

    // Case 2: min=2
    $html = Blade::render(<<<'BLADE'
        <x-mle-media-manager
            id="test-2"
            :model-reference="$model"
            :collections="['image' => 'images']"
            multiple
            :options="['minMediaCount' => 2]"
        />
    BLADE, ['model' => $model]);

    expect($html)->toContain('This collection requires at least 2 items');

    // Case 3: single manager required
    $html = Blade::render(<<<'BLADE'
        <x-mle-media-manager-single
            id="test-3"
            :model-reference="$model"
            :collections="['image' => 'images']"
            required
        />
    BLADE, ['model' => $model]);

    expect($html)->toContain('One medium required');
});

it('includes max media text in supported files summary', function () {
    $model = Blog::create(['title' => 'test']);
    
    $html = Blade::render(<<<'BLADE'
        <x-mle-media-manager
            id="test-1"
            :model-reference="$model"
            :collections="['image' => 'images']"
            multiple
            :options="['maxMediaCount' => 5]"
        />
    BLADE, ['model' => $model]);

    expect($html)->toContain('This collection can contain up to 5 items');
});