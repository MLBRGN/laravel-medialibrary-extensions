<?php

use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\DestroyForm;

it('initializes with given properties', function () {
    $medium = $this->getMediaModel(123);

    $component = new DestroyForm(
        medium: $medium,
        id: 'delete-media-123',
        frontendTheme: 'bootstrap-5',
        useXhr: true
    );

    expect($component->medium)->toBe($medium)
        ->and($component->id)->toBe('delete-media-123')
        ->and($component->frontendTheme)->toBe('bootstrap-5')
        ->and($component->useXhr)->toBeTrue();
});

it('defaults to config value for useXhr when not set', function () {
    config(['media-library-extensions.use_xhr' => true]);

    $medium = $this->getMediaModel();

    $component = new DestroyForm(
        medium: $medium,
        id: 'delete-456',
        frontendTheme: 'plain',
        useXhr: null
    );

    $component->render();

    expect($component->useXhr)->toBeTrue();
});

it('renders the destroy-form partial view', function () {
    $medium = $this->getMediaModel();

    $component = new DestroyForm(
        medium: $medium,
        id: 'delete-btn',
        frontendTheme: 'plain',
        useXhr: false
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
});
