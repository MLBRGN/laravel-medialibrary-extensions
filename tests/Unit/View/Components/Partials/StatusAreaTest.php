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
    $component = new StatusArea(
        id: 'component-123',
        frontendTheme: 'custom-theme',
        initiatorId: 'uploader-xyz'
    );

    expect($component->id)->toBe('component-123')
        ->and($component->frontendTheme)->toBe('custom-theme')
        ->and($component->initiatorId)->toBe('uploader-xyz')
        ->and($component->status)->toBeNull(); // since session handling is commented out
});

it('renders the correct partial view', function () {
    $theme = 'custom-theme';
    $expectedView = "media-library-extensions::components.$theme.partial.status-area";

    ViewFacade::shouldReceive('make')
        ->with($expectedView, [], [])
        ->once()
        ->andReturn(Mockery::mock(ViewInstance::class));

    $component = new StatusArea(
        id: 'component-123',
        frontendTheme: $theme,
        initiatorId: 'uploader-xyz'
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(ViewInstance::class);
});
