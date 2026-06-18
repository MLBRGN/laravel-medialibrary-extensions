<?php

use Illuminate\Http\Request;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\DemoController;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;

beforeEach(function () {
    config()->set('medialibrary-extensions.media_disks.originals', 'originals');

    config()->set('filesystems.disks', [
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => '/storage',
            'visibility' => 'public',
        ],
        'media' => [
            'driver' => 'local',
            'root' => storage_path('app/public/media'),
            'visibility' => 'public',
        ],
        'media_demo' => [
            'driver' => 'local',
            'root' => storage_path('app/public/media_demo'),
            'visibility' => 'public',
        ],
        'originals' => [
            'driver' => 'local',
            'root' => storage_path('app/public/media_originals'),
            'visibility' => 'private',
        ],
    ]);

    Storage::fake('public');
    Storage::fake('media');
    Storage::fake('originals');
    Storage::fake('media_demo');
});

it('returns the unified demo view with bootstrap-5 theme by default', function () {
    $controller = new DemoController;

    $response = $controller(
        Request::create('/demo')
    );

    expect($response)->toBeInstanceOf(View::class)
        ->and($response->name())->toBe('medialibrary-extensions::demo.mle-unified')
        ->and($response->getData()['model'])->toBeInstanceOf(Alien::class)
        ->and($response->getData()['frontendTheme'])->toBe('bootstrap-5');

    expect(config('medialibrary-extensions.frontend_theme'))
        ->toBe('bootstrap-5');

    $model = $response->getData()['model'];

    expect(Alien::on('media_demo')->find($model->id))
        ->not()
        ->toBeNull();
});

it('uses the plain theme when requested', function () {
    $controller = new DemoController;

    $response = $controller(
        Request::create('/demo', 'GET', [
            'theme' => 'plain',
        ])
    );

    expect($response->name())
        ->toBe('medialibrary-extensions::demo.mle-unified');

    expect($response->getData()['frontendTheme'])
        ->toBe('plain');

    expect(config('medialibrary-extensions.frontend_theme'))
        ->toBe('plain');
});

it('uses existing Alien if present', function () {
    $existingAlien = Alien::on('media_demo')->create();

    $response = (new DemoController)(
        Request::create('/demo')
    );

    $model = $response->getData()['model'];

    expect($model->id)->toBe($existingAlien->id);
});

it('creates model if none exists', function () {
    expect(Alien::on('media_demo')->count())->toBe(0);

    (new DemoController)(
        Request::create('/demo')
    );

    expect(Alien::on('media_demo')->count())->toBe(1);
});

it('applies use_xhr from request', function () {
    $response = (new DemoController)(
        Request::create('/demo', 'GET', [
            'use_xhr' => false,
        ])
    );

    expect(config('medialibrary-extensions.use_xhr'))->toBeFalse();

    expect($response->getData()['useXhr'])->toBeFalse();
});
