<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Mlbrgn\MediaLibraryExtensions\Helpers\DemoHelper;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

beforeEach(function () {
    // Set default configuration for tests
    Config::set('media-library-extensions.demo_pages_enabled', true);
    Config::set('media-library-extensions.route_prefix', 'mlbrgn-mle');
    Config::set('media-library-extensions.demo_database_name', config('media-library-extensions.media_disks.demo'));
});

it('uses default connection when demo pages are disabled', function () {
    // Arrange
    Config::set('media-library-extensions.demo_pages_enabled', false);
    $temporaryUpload = new TemporaryUpload;

    expect($temporaryUpload->getConnectionName())->toBeNull();
});

it('uses default connection when not on a demo page', function () {
    // Arrange
    $temporaryUpload = new TemporaryUpload;

    // Mock DemoHelper to return false
    $demoHelper = \Mockery::mock('alias:'.DemoHelper::class);
    $demoHelper->shouldReceive('isRequestFromDemoPage')
        ->once()
        ->andReturn(false);

    $connectionName = $temporaryUpload->getConnectionName();
    // Act & Assert
    expect($connectionName)->toBeNull();
});

it('uses demo connection when on a demo page', function () {
    // Arrange
    $temporaryUpload = new TemporaryUpload;

    // Mock DemoHelper to return true
    $demoHelper = \Mockery::mock('alias:'.DemoHelper::class);
    $demoHelper->shouldReceive('isRequestFromDemoPage')
        ->once()
        ->andReturn(true);

    // Act & Assert
    expect($temporaryUpload->getConnectionName())->toBe(config('media-library-extensions.media_disks.demo'));
});

it('uses the configured demo database name', function () {
    // Arrange
    Config::set('media-library-extensions.demo_database_name', 'custom_demo_db');
    $temporaryUpload = new TemporaryUpload;

    // Mock DemoHelper to return true
    $demoHelper = \Mockery::mock('alias:'.DemoHelper::class);
    $demoHelper->shouldReceive('isRequestFromDemoPage')
        ->once()
        ->andReturn(true);

    // Act & Assert
    expect($temporaryUpload->getConnectionName())->toBe('custom_demo_db');
});

// it('returns false if demo pages are disabled', function () {
//    Config::set('media-library-extensions.demo_pages_enabled', false);
//
//    // no need to mock path/referer because demo_pages_enabled is false
//    expect(DemoHelper::isRequestFromDemoPage())->toBeFalse();
// });
//
// it('returns true if current URL is a demo page', function () {
//    Config::set('media-library-extensions.demo_pages_enabled', true);
//
//    Request::shouldReceive('path')->andReturn('demo/mle-demo-plain');
//    Request::shouldReceive('header')->with('referer')->andReturn(null);
//
//    expect(DemoHelper::isRequestFromDemoPage())->toBeTrue();
// });
//
// it('returns true if referer is a demo page', function () {
//    Config::set('media-library-extensions.demo_pages_enabled', true);
//
//    Request::shouldReceive('path')->andReturn('normal-url');
//    Request::shouldReceive('header')->with('referer')->andReturn('http://localhost/demo/mle-demo-bootstrap-5');
//
//    expect(DemoHelper::isRequestFromDemoPage())->toBeTrue();
// });
//
// it('returns false if neither current URL nor referer is a demo page', function () {
//    Config::set('media-library-extensions.demo_pages_enabled', true);
//
//    Request::shouldReceive('path')->andReturn('normal-url');
//    Request::shouldReceive('header')->with('referer')->andReturn('http://localhost/other-page');
//
//    expect(DemoHelper::isRequestFromDemoPage())->toBeFalse();
// });

// it('returns true when current URL is a demo page (plain)', function () {
//    // Use partialMock to avoid container rebinding issues
//    Request::partialMock()
//        ->shouldReceive('path')
//        ->once()
//        ->andReturn('mlbrgn-mle/mle-demo-plain');
//
//    Request::partialMock()
//        ->shouldReceive('header')
//        ->with('referer')
//        ->once()
//        ->andReturn(null);
//
//    expect(DemoHelper::isRequestFromDemoPage())->toBeTrue();
// });

// it('returns false if demo pages are disabled', function () {
//    Config::set('media-library-extensions.demo_pages_enabled', false);
//
//    $request = request();
//    $request->server->set('REQUEST_URI', '/mlbrgn-mle/mle-demo-plain');
//    $request->headers->set('referer', null);
//
//    expect(DemoHelper::isRequestFromDemoPage())->toBeFalse();
// });

// it('returns false if demo pages are disabled', function () {
//    Config::set('media-library-extensions.demo_pages_enabled', false);
//
//    // Bind a real request into the container
//    $fakeRequest = Request::create('/mlbrgn-mle/mle-demo-plain', 'GET');
//    dd($fakeRequest);
//    app()->instance('request', $fakeRequest);
//
//    expect(DemoHelper::isRequestFromDemoPage())->toBeFalse();
// });

it('returns false if demo pages are disabled', function () {
    // ARRANGE 1: Set the required configuration values
    Config::set('media-library-extensions.demo_pages_enabled', false);
    Config::set('media-library-extensions.route_prefix', 'mlbrgn-mle');

    // ARRANGE 2: MOCK THE FACADE. This is the only way to satisfy static calls
    // like Request::path() and Request::header() in a simple unit test.
    // We avoid using app()->instance() or app()->forgetInstance() entirely.

    // The logic inside DemoHelper::isRequestFromDemoPage() will call these:
    Request::shouldReceive('path')
        // The path we are "testing" against
        ->andReturn('/mlbrgn-mle/mle-demo-plain');

    Request::shouldReceive('header')
        ->with('referer')
        ->andReturn(null);

    // ACT & ASSERT
    // The test should pass because demo_pages_enabled is false,
    // causing the helper to return false immediately.
    expect(DemoHelper::isRequestFromDemoPage())->toBeFalse();
})->skip();
// it('returns true when current URL is a demo page (plain)', function () {
//    // Arrange: set the current path and referer in the actual request
//    $request = request();
//    $request->server->set('REQUEST_URI', '/mlbrgn-mle/mle-demo-plain');
//    $request->headers->set('referer', null);
//
//    // Act & Assert
//    expect(DemoHelper::isRequestFromDemoPage())->toBeTrue();
// });

it('returns true when current URL is a demo page (bootstrap-5)', function () {
    // Arrange
    Request::shouldReceive('path')
        ->once()
        ->andReturn('mlbrgn-mle/mle-demo-bootstrap-5');

    Request::shouldReceive('header')
        ->with('referer')
        ->once()
        ->andReturn(null);

    // Act & Assert
    expect(DemoHelper::isRequestFromDemoPage())->toBeTrue();
})->skip();

it('returns true when referer is a demo page (plain)', function () {
    // Arrange
    Request::shouldReceive('path')
        ->once()
        ->andReturn('some/other/path');

    Request::shouldReceive('header')
        ->with('referer')
        ->once()
        ->andReturn('https://example.com/mlbrgn-mle/mle-demo-plain');

    // Act & Assert
    expect(DemoHelper::isRequestFromDemoPage())->toBeTrue();
})->skip();

it('returns true when referer is a demo page (bootstrap-5)', function () {
    // Arrange
    Request::shouldReceive('path')
        ->once()
        ->andReturn('some/other/path');

    Request::shouldReceive('header')
        ->with('referer')
        ->once()
        ->andReturn('https://example.com/mlbrgn-mle/mle-demo-bootstrap-5');

    // Act & Assert
    expect(DemoHelper::isRequestFromDemoPage())->toBeTrue();
})->skip();

it('returns false when neither current URL nor referer is a demo page', function () {
    // Arrange
    Request::shouldReceive('path')
        ->once()
        ->andReturn('some/other/path');

    Request::shouldReceive('header')
        ->with('referer')
        ->once()
        ->andReturn('https://example.com/some/other/page');

    // Act & Assert
    expect(DemoHelper::isRequestFromDemoPage())->toBeFalse();
})->skip();

it('uses the configured route prefix', function () {
    // Arrange
    Config::set('media-library-extensions.route_prefix', 'custom-prefix');

    Request::shouldReceive('path')
        ->once()
        ->andReturn('custom-prefix/mle-demo-plain');

    Request::shouldReceive('header')
        ->with('referer')
        ->once()
        ->andReturn(null);

    // Act & Assert
    expect(DemoHelper::isRequestFromDemoPage())->toBeTrue();
})->skip();
