<?php

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\Spinner;

it('renders the spinner partial view and sets properties correctly', function () {
    $id = 'spinner-1';
    $component = new Spinner(
        id: $id,
        initiatorId: 'initiator-123',
        mediaManagerDomId: 'test-media-manager-dom-id',
        options: [
            'frontendTheme' => 'plain',
        ]
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class)
        ->and($component->domId)->toBe($id)
        ->and($component->mediaManagerDomId)->toBe('test-media-manager-dom-id')
        ->and($component->getConfig('frontendTheme'))->toBe('plain')
        ->and($component->initiatorId)->toBe('initiator-123');
});
