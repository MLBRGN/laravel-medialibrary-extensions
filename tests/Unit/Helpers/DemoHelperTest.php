<?php

//
// use Illuminate\Support\Facades\Config;
// use Illuminate\Support\Facades\Request;
// use Mlbrgn\MediaLibraryExtensions\Helpers\DemoHelper;
//
// beforeEach(function () {
//    // Set default configuration for tests
//    Config::set('media-library-extensions.demo_pages_enabled', true);
//    Config::set('media-library-extensions.route_prefix', 'mlbrgn-mle');
// });
//
// it('returns false when demo pages are disabled', function () {
//    // Arrange
//    Config::set('media-library-extensions.demo_pages_enabled', false);
//
//    // Act & Assert
//    expect(DemoHelper::isRequestFromDemoPage())->toBeFalse();
// })->skip();
//
// it('returns true when current URL is a demo page (plain)', function () {
//    // Arrange
//    Request::shouldReceive('path')
//        ->once()
//        ->andReturn('mlbrgn-mle/mle-demo-plain');
//
//    Request::shouldReceive('header')
//        ->with('referer')
//        ->once()
//        ->andReturn(null);
//
//    // Act & Assert
//    expect(DemoHelper::isRequestFromDemoPage())->toBeTrue();
// })->skip();
//
// it('returns true when current URL is a demo page (bootstrap-5)', function () {
//    // Arrange
//    Request::shouldReceive('path')
//        ->once()
//        ->andReturn('mlbrgn-mle/mle-demo-bootstrap-5');
//
//    Request::shouldReceive('header')
//        ->with('referer')
//        ->once()
//        ->andReturn(null);
//
//    // Act & Assert
//    expect(DemoHelper::isRequestFromDemoPage())->toBeTrue();
// })->skip();
//
// it('returns true when referer is a demo page (plain)', function () {
//    // Arrange
//    Request::shouldReceive('path')
//        ->once()
//        ->andReturn('some/other/path');
//
//    Request::shouldReceive('header')
//        ->with('referer')
//        ->once()
//        ->andReturn('https://example.com/mlbrgn-mle/mle-demo-plain');
//
//    // Act & Assert
//    expect(DemoHelper::isRequestFromDemoPage())->toBeTrue();
// })->skip();
//
// it('returns true when referer is a demo page (bootstrap-5)', function () {
//    // Arrange
//    Request::shouldReceive('path')
//        ->once()
//        ->andReturn('some/other/path');
//
//    Request::shouldReceive('header')
//        ->with('referer')
//        ->once()
//        ->andReturn('https://example.com/mlbrgn-mle/mle-demo-bootstrap-5');
//
//    // Act & Assert
//    expect(DemoHelper::isRequestFromDemoPage())->toBeTrue();
// })->skip();
//
// it('returns false when neither current URL nor referer is a demo page', function () {
//    // Arrange
//    Request::shouldReceive('path')
//        ->once()
//        ->andReturn('some/other/path');
//
//    Request::shouldReceive('header')
//        ->with('referer')
//        ->once()
//        ->andReturn('https://example.com/some/other/page');
//
//    // Act & Assert
//    expect(DemoHelper::isRequestFromDemoPage())->toBeFalse();
// })->skip();
//
// it('uses the configured route prefix', function () {
//    // Arrange
//    Config::set('media-library-extensions.route_prefix', 'custom-prefix');
//
//    Request::shouldReceive('path')
//        ->once()
//        ->andReturn('custom-prefix/mle-demo-plain');
//
//    Request::shouldReceive('header')
//        ->with('referer')
//        ->once()
//        ->andReturn(null);
//
//    // Act & Assert
//    expect(DemoHelper::isRequestFromDemoPage())->toBeTrue();
// })->skip();
