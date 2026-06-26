<?php

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\Spinner;

it('renders the spinner partial view and sets properties correctly', function () {
    $id = 'test-1';
    $component = new Spinner(
        id: $id,
        options: [
            'frontendTheme' => 'plain',
        ]
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class)
        ->and($component->getDomId())->toBe($id.'-spinner')
        ->and($component->getConfig('frontendTheme'))->toBe('plain');
});
