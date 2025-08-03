<?php

use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewInstance;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;
use Mlbrgn\MediaLibraryExtensions\Traits\ViewHelpers;

uses(TestCase::class);

class ViewHelpersTestDummy
{
    use ViewHelpers;
}

beforeEach(function () {
    View::shouldReceive('exists')->zeroOrMoreTimes(); // In case it's ever uncommented
});

it('returns themed view path for getView', function () {
    $theme = 'custom';
    $viewName = 'example';
    $expectedViewPath = "media-library-extensions::components.$theme.$viewName";

    View::shouldReceive('make')
        ->with($expectedViewPath, [], [])
        ->once()
        ->andReturn(Mockery::mock(ViewInstance::class));

    $dummy = new ViewHelpersTestDummy;
    $view = $dummy->getView($viewName, $theme);

    expect($view)->toBeInstanceOf(ViewInstance::class);
});

it('returns themed partial view path for getPartialView', function () {
    $theme = 'custom';
    $viewName = 'example-partial';
    $expectedViewPath = "media-library-extensions::components.$theme.partial.$viewName";

    View::shouldReceive('make')
        ->with($expectedViewPath, [], [])
        ->once()
        ->andReturn(Mockery::mock(ViewInstance::class));

    $dummy = new ViewHelpersTestDummy;
    $view = $dummy->getPartialView($viewName, $theme);

    expect($view)->toBeInstanceOf(ViewInstance::class);
});
