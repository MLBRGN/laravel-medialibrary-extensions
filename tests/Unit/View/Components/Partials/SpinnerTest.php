<?php

use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\Spinner;

it('renders the spinner partial view and sets properties correctly', function () {
    $id = 'spinner-1';
    $component = new Spinner(
        id: $id,
        initiatorId: 'initiator-123',
        mediaManagerId: 'test-media-manager-id',
        options: [
            'frontendTheme' => 'plain',
        ]
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class)
        ->and($component->id)->toBe($id)
        ->and($component->mediaManagerId)->toBe('test-media-manager-id')
        ->and($component->getConfig('frontendTheme'))->toBe('plain')
        ->and($component->initiatorId)->toBe('initiator-123');
});
