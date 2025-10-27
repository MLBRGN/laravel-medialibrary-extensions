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
    $initiatorId = 'media-manager-125';
    $mediaManagerId = 'media-manager-125';
    $component = new StatusArea(
        id: 'component-123',
        initiatorId: $initiatorId,
        mediaManagerId: $mediaManagerId,
        options: [
            'frontendTheme' => 'custom-theme',
        ],
    );

    expect($component->id)->toBe('component-123')
        ->and($component->getConfig('frontendTheme'))->toBe('custom-theme')
        ->and($component->initiatorId)->toBe($initiatorId)
        ->and($component->mediaManagerId)->toBe($mediaManagerId);
});

it('renders the correct partial view', function () {
    $initiatorId = 'media-manager-125';
    $mediaManagerId = 'media-manager-125';
    $theme = 'bootstrap-5';
    $expectedView = "media-library-extensions::components.$theme.partial.status-area";

    ViewFacade::shouldReceive('make')
        ->with($expectedView, [], [])
        ->once()
        ->andReturn(Mockery::mock(ViewInstance::class));

    $component = new StatusArea(
        id: 'component-123',
        initiatorId: $initiatorId,
        mediaManagerId: $mediaManagerId,
        options: [
            'frontendTheme' => $theme,
        ],
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(ViewInstance::class)
        ->and($component->getConfig('frontendTheme'))->toBe('bootstrap-5');
});
