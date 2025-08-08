<?php

use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\Spinner;

it('renders the spinner partial view and sets properties correctly', function () {
    $component = new Spinner(
        id: 'spinner-1',
        frontendTheme: 'plain',
        initiatorId: 'initiator-123',
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
    expect($component->id)->toBe('spinner-1');
    expect($component->frontendTheme)->toBe('plain');
    expect($component->initiatorId)->toBe('initiator-123');
});
