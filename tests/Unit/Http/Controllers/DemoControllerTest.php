<?php

use Mlbrgn\MediaLibraryExtensions\Http\Controllers\DemoController;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;

it('sets config and returns view with model for demoPlain', function () {
    $controller = new DemoController;
    $response = $controller->demoPlain();

    expect($response)->toBeInstanceOf(\Illuminate\View\View::class)
        ->and($response->name())->toBe('media-library-extensions::components.demo.mle-plain')
        ->and($response->getData()['model'])->toBeInstanceOf(Alien::class)
        ->and(config('media-library-extensions.frontend_theme'))->toBe('plain');

    $model = $response->getData()['model'];
    expect($model)->toBeInstanceOf(Alien::class);
    expect(Alien::find($model->id))->not()->toBeNull();
});

it('sets config and returns view with model for demoBootstrap5', function () {
    $controller = new DemoController;
    $response = $controller->demoBootstrap5();

    expect(config('media-library-extensions.frontend_theme'))->toBe('bootstrap-5');
    expect($response->name())->toBe('media-library-extensions::components.demo.mle-bootstrap-5');

    $model = $response->getData()['model'];
    expect($model)->toBeInstanceOf(Alien::class);
    expect(Alien::find($model->id))->not()->toBeNull();
});

it('uses existing Alien if present for demoPlain', function () {
    $existingAlien = Alien::create();

    $controller = new DemoController;
    $response = $controller->demoPlain();

    $model = $response->getData()['model'];
    expect($model->id)->toBe($existingAlien->id);
});

it('uses existing Alien if present for demoBootstrap5', function () {
    $existingAlien = Alien::create();
    $controller = new DemoController;
    $response = $controller->demoBootstrap5();

    $model = $response->getData()['model'];
    expect($model->id)->toBe($existingAlien->id);
});

it('creates model if none exists', function () {
    expect(Alien::count())->toBe(0);

    (new DemoController)->demoPlain();

    expect(Alien::count())->toBe(1);
});
