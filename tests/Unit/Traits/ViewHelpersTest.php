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
    $expectedViewPath = "media-library-extensions::components.$frontendTheme.$viewName";

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
    $expectedViewPath = "media-library-extensions::components.$frontendTheme.partial.$viewName";

    View::shouldReceive('make')
        ->with($expectedViewPath, [], [])
        ->once()
        ->andReturn(Mockery::mock(ViewInstance::class));

    $dummy = new ViewHelpersTest;
    $view = $dummy->getPartialView($viewName, $frontendTheme);

    expect($view)->toBeInstanceOf(ViewInstance::class);
});
