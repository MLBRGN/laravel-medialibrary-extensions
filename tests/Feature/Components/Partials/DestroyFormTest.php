<?php

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\DestroyForm;

it('initializes with given properties', function () {
    $model = $this->getModelWithMedia(['image' => 2]);
    $id = 'some-id';
    $medium = $this->getMediaModel(123);

    $component = new DestroyForm(
        id: $id,
        modelOrClassName: $model,
        medium: $medium,
        options: [
            'theme' => 'bootstrap-5',
            'useXhr' => true,
        ]
    );

    expect($component->medium)->toBe($medium)
        ->and($component->getDomId())->toBe('some-id-destroy-form-123')
        ->and($component->getOptions())->toMatchArray([
            'theme' => 'bootstrap-5',
            'useXhr' => true,
        ]);
});

it('initializes with given properties without useXhr', function () {
    config(['medialibrary-extensions.use_xhr' => false]);
    $model = $this->getModelWithMedia(['image' => 2]);

    $medium = $this->getMediaModel();

    $component = new DestroyForm(
        id: 'delete-456',
        modelOrClassName: $model,
        medium: $medium,
        options: [
            'theme' => 'plain',
        ]
    );

    $component->render();

    expect($component->medium)->toBe($medium)
        ->and($component->getDomId())->toBe('delete-456-destroy-form-1')
        ->and($component->getConfig('theme'))->toBe('plain')
        ->and($component->getConfig('useXhr'))->toBeFalse();
});

it('renders the destroy-form view (plain)', function () {
    $medium = $this->getMediaModel();
    $model = $this->getModelWithMedia(['image' => 2]);

    $component = new DestroyForm(
        id: 'delete-btn',
        modelOrClassName: $model,
        medium: $medium,
        options: [
            'theme' => 'plain',
            'useXhr' => true,
        ]
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
});

it('renders the destroy-form view (bootstrap-5)', function () {
    $medium = $this->getMediaModel();
    $model = $this->getModelWithMedia(['image' => 2]);

    $component = new DestroyForm(
        id: 'delete-btn',
        modelOrClassName: $model,
        medium: $medium,
        options: [
            'theme' => 'bootstrap-5',
            'useXhr' => true,
        ]
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
});

it('renders the destroy form with temporary upload', function () {
    $temporaryUpload = new TemporaryUpload([
        'uuid' => 'test-uuid',
        'file_name' => 'test.jpg',
        'collection_name' => 'temp-uploads',
        'disk' => 'media',
        'mime_type' => 'image/jpeg',
        'custom_properties' => [],
    ]);

    // Explicitly set the primary key since it's guarded and not fillable
    $temporaryUpload->setAttribute('id', 1);

    $component = new DestroyForm(
        id: 'delete-temp-upload-btn',
        modelOrClassName: Blog::class,
        medium: $temporaryUpload,
        collections: [],
        options: [],
        disabled: false,
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
});
