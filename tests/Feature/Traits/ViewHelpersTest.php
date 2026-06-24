<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\Traits;

use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewInstance;
use Mlbrgn\MediaLibraryExtensions\Traits\ViewHelpers;
use Mockery;

class ViewHelpersTest
{
    use ViewHelpers;
}

beforeEach(function () {
    View::shouldReceive('exists')->zeroOrMoreTimes(); // In case it's ever uncommented
});

it('returns themed view path for getView', function () {
    $frontendTheme = 'custom';
    $viewName = 'example';
    $expectedViewPath = "medialibrary-extensions::components.$frontendTheme.$viewName";

    View::shouldReceive('make')
        ->with($expectedViewPath, [], [])
        ->once()
        ->andReturn(Mockery::mock(ViewInstance::class));

    $dummy = new ViewHelpersTest;
    $view = $dummy->getView($viewName, $frontendTheme);

    expect($view)->toBeInstanceOf(ViewInstance::class);
});

it('returns themed partial view path for getPartialView', function () {
    $frontendTheme = 'custom';
    $viewName = 'example-partial';
    $expectedViewPath = "medialibrary-extensions::components.$frontendTheme.partial.$viewName";

    View::shouldReceive('make')
        ->with($expectedViewPath, [], [])
        ->once()
        ->andReturn(Mockery::mock(ViewInstance::class));

    $dummy = new ViewHelpersTest;
    $view = $dummy->getPartialView($viewName, $frontendTheme);

    expect($view)->toBeInstanceOf(ViewInstance::class);
});

it('handles null frontendTheme by using config fallback', function () {
    $expectedViewPath = 'medialibrary-extensions::components.bootstrap-5.media-manager';
    View::shouldReceive('make')
        ->with($expectedViewPath, [], [])
        ->once()
        ->andReturn(Mockery::mock(ViewInstance::class));

    $dummy = new ViewHelpersTest;
    $view = $dummy->getView('media-manager', null);

    expect($view)->toBeInstanceOf(ViewInstance::class);
});

it('handles null frontendTheme for partial by using config fallback', function () {
    $expectedViewPath = 'medialibrary-extensions::components.bootstrap-5.partial.upload-form';
    View::shouldReceive('make')
        ->with($expectedViewPath, [], [])
        ->once()
        ->andReturn(Mockery::mock(ViewInstance::class));

    $dummy = new ViewHelpersTest;
    $view = $dummy->getPartialView('upload-form', null);

    expect($view)->toBeInstanceOf(ViewInstance::class);
});

it('uses custom config frontendTheme when provided', function () {
    config(['medialibrary-extensions.frontend_theme' => 'plain']);
    $expectedViewPath = 'medialibrary-extensions::components.plain.media-manager';
    View::shouldReceive('make')
        ->with($expectedViewPath, [], [])
        ->once()
        ->andReturn(Mockery::mock(ViewInstance::class));

    $dummy = new ViewHelpersTest;
    $view = $dummy->getView('media-manager', null);

    expect($view)->toBeInstanceOf(ViewInstance::class);
});
