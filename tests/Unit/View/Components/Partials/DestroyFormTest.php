<?php

use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\DestroyForm;

it('initializes with given properties', function () {
    $id = 'some-id';
    $medium = $this->getMediaModel(123);

    $component = new DestroyForm(
        id: $id,
        medium: $medium,
        options: [
            'frontendTheme' => 'bootstrap-5',
            'useXhr' => true,
        ]
    );

    expect($component->medium)->toBe($medium)
        ->and($component->id)->toBe('some-id-destroy-form-123')
        ->and($component->options)->toMatchArray([
            'frontendTheme' => 'bootstrap-5',
            'useXhr' => true,
        ]);
});

it('initializes with given properties without useXhr', function () {
    config(['media-library-extensions.use_xhr' => false]);

    $medium = $this->getMediaModel();

    $component = new DestroyForm(
        id: 'delete-456',
        medium: $medium,
        options: [
            'frontendTheme' => 'plain',
        ]
    );

    $component->render();

    //    dd($component->options);
    expect($component->medium)->toBe($medium)
        ->and($component->id)->toBe('delete-456-destroy-form-1')
        ->and($component->getConfig('frontendTheme'))->toBe('plain')
        ->and($component->getConfig('useXhr'))->toBeFalse();
});

it('renders the destroy-form partial view (plain)', function () {
    $medium = $this->getMediaModel();

    $component = new DestroyForm(
        id: 'delete-btn',
        medium: $medium,
        options: [
            'frontendTheme' => 'plain',
            'useXhr' => true,
        ]
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
});

it('renders the destroy-form partial view (bootstrap-5)', function () {
    $medium = $this->getMediaModel();

    $component = new DestroyForm(
        id: 'delete-btn',
        medium: $medium,
        options: [
            'frontendTheme' => 'bootstrap-5',
            'useXhr' => true,
        ]
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
});
