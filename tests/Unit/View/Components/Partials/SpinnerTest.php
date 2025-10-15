<?php

use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\Spinner;

it('renders the spinner partial view and sets properties correctly', function () {
    $id = 'spinner-1';
    $component = new Spinner(
        id: $id,
        frontendTheme: 'plain',
        initiatorId: 'initiator-123',
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class)
        ->and($component->id)->toBe($id)
        ->and($component->frontendTheme)->toBe('plain')
        ->and($component->initiatorId)->toBe('initiator-123');
});
