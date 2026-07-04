<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature\Traits;

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
    $theme = 'custom';
    $viewName = 'example';
    $expectedViewPath = "medialibrary-extensions::components.$theme.$viewName";

    View::shouldReceive('make')
        ->with($expectedViewPath, [], [])
        ->once()
        ->andReturn(Mockery::mock(ViewInstance::class));

    $dummy = new ViewHelpersTest;
    $view = $dummy->getView($viewName, $theme);

    expect($view)->toBeInstanceOf(ViewInstance::class);
});

it('returns themed partial view path for getPartialView', function () {
    $theme = 'custom';
    $viewName = 'example-partial';
    $expectedViewPath = "medialibrary-extensions::components.$theme.partial.$viewName";

    View::shouldReceive('make')
        ->with($expectedViewPath, [], [])
        ->once()
        ->andReturn(Mockery::mock(ViewInstance::class));

    $dummy = new ViewHelpersTest;
    $view = $dummy->getPartialView($viewName, $theme);

    expect($view)->toBeInstanceOf(ViewInstance::class);
});

it('handles null theme by using config fallback', function () {
    $expectedViewPath = 'medialibrary-extensions::components.bootstrap-5.media-manager';
    View::shouldReceive('make')
        ->with($expectedViewPath, [], [])
        ->once()
        ->andReturn(Mockery::mock(ViewInstance::class));

    $dummy = new ViewHelpersTest;
    $view = $dummy->getView('media-manager', null);

    expect($view)->toBeInstanceOf(ViewInstance::class);
});

it('handles null theme for partial by using config fallback', function () {
    $expectedViewPath = 'medialibrary-extensions::components.bootstrap-5.partial.upload-form';
    View::shouldReceive('make')
        ->with($expectedViewPath, [], [])
        ->once()
        ->andReturn(Mockery::mock(ViewInstance::class));

    $dummy = new ViewHelpersTest;
    $view = $dummy->getPartialView('upload-form', null);

    expect($view)->toBeInstanceOf(ViewInstance::class);
});

it('uses custom config theme when provided', function () {
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
