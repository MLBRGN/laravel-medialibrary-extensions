<?php

use Illuminate\Http\Request;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Http\Controllers\DemoController;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
use Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure;

beforeEach(function () {
    config()->set('medialibrary-extensions.media_disks.originals', 'originals');
    config(['medialibrary-extensions.demo_pages_enabled' => true]);
    config()->set('medialibrary-extensions.demo_pages_enabled', true);

    PackageInfrastructure::register('demo');

    Storage::fake('public');
    Storage::fake('media');
    Storage::fake('originals');
    Storage::fake('media_demo');
});

it('can render the demo pge', function () {

    $user = $this->getUser();
    $route = route('mle-demo');

    $response = $this
        ->actingAs($user)
        ->followingRedirects()
        ->get($route);

    $response->assertOk()
        ->assertViewIs('medialibrary-extensions::demo.mle-unified');
    expect(config('medialibrary-extensions.frontend_theme'))
        ->toBe('bootstrap-5');

    $model = $response->viewData('model');

    expect($model)
        ->toBeInstanceOf(Alien::class);

    expect(Alien::on(PackageInfrastructure::connection('demo', 'default'))->find($model->id))
        ->not()
        ->toBeNull();
});

//it('uses the plain theme when requested', function () {
//    $user = $this->getUser();
//    $route = route('mle-demo');
//
//    $response = $this
//        ->actingAs($user)
//        ->followingRedirects()
//        ->get($route);
//
//    $response->assertOk()
//        ->assertViewIs('medialibrary-extensions::demo.mle-unified');
//
//    $model = $response->viewData('model');
//
//    expect($model)
//        ->toBeInstanceOf(Alien::class);
//
//    expect(Alien::on(PackageInfrastructure::connection('demo', 'default'))->find($model->id))
//        ->not()
//        ->toBeNull();
//
//});

//it('uses existing Alien if present', function () {
//    $existingAlien = Alien::on('mle_test_demo')->create();
//
//    $response = (new DemoController)->index(
//        Request::create('/demo')
//    );
//
//    $model = $response->getData()['model'];
//
//    expect($model->id)->toBe($existingAlien->id);
//});
//
//it('creates model if none exists', function () {
//    expect(Alien::on('mle_test_demo')->count())->toBe(0);
//
//    (new DemoController)->index(
//        Request::create('/demo')
//    );
//
//    expect(Alien::on('mle_test_demo')->count())->toBe(1);
//});
//
//it('applies use_xhr from request', function () {
//    $response = (new DemoController)->index(
//        Request::create('/demo', 'GET', [
//            'use_xhr' => false,
//        ])
//    );
//
//    expect(config('medialibrary-extensions.use_xhr'))->toBeFalse();
//
//    expect($response->getData()['useXhr'])->toBeFalse();
//});
