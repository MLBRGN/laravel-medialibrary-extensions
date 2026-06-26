<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View as ViewInstance;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\StatusArea;

beforeEach(function () {
    // Optionally reset session
    Session::flush();
});

it('initializes component with provided data', function () {
    $id = 'component-123';
    $component = new StatusArea(
        id: $id,
        options: [
            'frontendTheme' => 'custom-theme',
        ],
    );

    expect($component->getDomId())->toBe($id.'-status-area')
        ->and($component->getConfig('frontendTheme'))->toBe('custom-theme');
});

it('renders the correct partial view', function () {
    $theme = 'bootstrap-5';
    $expectedView = "medialibrary-extensions::components.$theme.partial.status-area";

    ViewFacade::shouldReceive('make')
        ->with($expectedView, [], [])
        ->once()
        ->andReturn(Mockery::mock(ViewInstance::class));

    $component = new StatusArea(
        id: 'component-123',
        options: [
            'frontendTheme' => $theme,
        ],
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(ViewInstance::class)
        ->and($component->getConfig('frontendTheme'))->toBe('bootstrap-5');
});
