<?php

use Mlbrgn\MediaLibraryExtensions\Tests\TestSupport\Models\Post;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaGallery;
use Illuminate\Support\Facades\Blade;

beforeEach(function () {
    $this->model = Post::create(['title' => 'Test Post']);
});

it('can render the media gallery component', function () {
    $this->model->addMedia($this->getTestFilePath('test.jpg'))->preservingOriginal()->toMediaCollection('images');

    $view = $this->blade(
        '<x-mle-media-gallery id="my-gallery" :model-reference="$model" :collections="[\'images\']" />',
        ['model' => $this->model]
    );

    $view->assertSee('mle-media-gallery');
    $view->assertSee('mle-media-gallery-grid');
    $view->assertSee('my-gallery-gal');
    $view->assertSee('test.jpg');
});

it('renders multiple collections', function () {
    $this->model->addMedia($this->getTestFilePath('test.jpg'))->preservingOriginal()->toMediaCollection('images');
    $this->model->addMedia($this->getTestFilePath('test.png'))->preservingOriginal()->toMediaCollection('other');

    $view = $this->blade(
        '<x-mle-media-gallery id="my-gallery" :model-reference="$model" :collections="[\'images\', \'other\']" />',
        ['model' => $this->model]
    );

    $view->assertSee('test.jpg');
    $view->assertSee('test.png');
});

it('applies grid layout properties', function () {
    $this->model->addMedia($this->getTestFilePath('test.jpg'))->preservingOriginal()->toMediaCollection('images');

    $view = $this->blade(
        '<x-mle-media-gallery id="my-gallery" :model-reference="$model" :collections="[\'images\']" layout="grid" :columns="4" gap="2rem" />',
        ['model' => $this->model]
    );

    $view->assertSee('mle-media-gallery-grid');
    $view->assertSee('--mle-gallery-columns: 4');
    $view->assertSee('--mle-gallery-gap: 2rem');
});

it('applies flex layout', function () {
    $this->model->addMedia($this->getTestFilePath('test.jpg'))->preservingOriginal()->toMediaCollection('images');

    $view = $this->blade(
        '<x-mle-media-gallery id="my-gallery" :model-reference="$model" :collections="[\'images\']" layout="flex" />',
        ['model' => $this->model]
    );

    $view->assertSee('mle-media-gallery-flex');
});

it('renders the modal when expandable', function () {
    $this->model->addMedia($this->getTestFilePath('test.jpg'))->preservingOriginal()->toMediaCollection('images');

    $view = $this->blade(
        '<x-mle-media-gallery id="my-gallery" :model-reference="$model" :collections="[\'images\']" :expandable="true" />',
        ['model' => $this->model]
    );

    $view->assertSee('data-bs-toggle="modal"', false);
    $view->assertSee('data-bs-target="#my-gallery-mod"', false);
    $view->assertSee('mle-media-modal');
    $view->assertSee('id="my-gallery-mod"', false);
});

it('does not render the modal when not expandable', function () {
    $this->model->addMedia($this->getTestFilePath('test.jpg'))->preservingOriginal()->toMediaCollection('images');

    $view = $this->blade(
        '<x-mle-media-gallery id="my-gallery" :model-reference="$model" :collections="[\'images\']" :expandable="false" />',
        ['model' => $this->model]
    );

    $view->assertDontSee('data-bs-toggle="modal"', false);
    $view->assertDontSee('mle-media-modal');
});

it('verifies modal duplication concern', function () {
    $this->model->addMedia($this->getTestFilePath('test.jpg'))->preservingOriginal()->toMediaCollection('images');

    $view = $this->blade(
        '<x-mle-media-gallery id="my-gallery" :model-reference="$model" :collections="[\'images\']" :expandable="true" />',
        ['model' => $this->model]
    );

    // Count occurrences of the modal container precisely
    $html = (string) $view;
    $count = substr_count($html, 'data-mle-media-modal');
    
    // We expect 1. If duplicated by children, it would be more.
    expect($count)->toBe(1);
});
