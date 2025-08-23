<?php

use Mlbrgn\MediaLibraryExtensions\View\Components\Shared\Assets;

it('sets default values from config', function () {
    config(['media-library-extensions.frontend_theme' => 'default-theme']);

    $component = new Assets();

    expect($component->frontendTheme)->toBe('default-theme')
        ->and($component->includeCss)->toBeFalse()
        ->and($component->includeJs)->toBeFalse()
        ->and($component->includeImageEditorJs)->toBeFalse()
        ->and($component->includeFormSubmitter)->toBeFalse()
        ->and($component->includeLiteYoutube)->toBeFalse();
});

it('accepts custom constructor values', function () {
    $component = new Assets(
        frontendTheme: 'custom-theme',
        includeCss: true,
        includeJs: true,
        includeImageEditorJs: true,
        includeFormSubmitter: true,
        includeLiteYoutube: true,
    );

    expect($component->frontendTheme)->toBe('custom-theme')
        ->and($component->includeCss)->toBeTrue()
        ->and($component->includeJs)->toBeTrue()
        ->and($component->includeImageEditorJs)->toBeTrue()
        ->and($component->includeFormSubmitter)->toBeTrue()
        ->and($component->includeLiteYoutube)->toBeTrue();
});

it('renders the correct view', function () {
    $component = new Assets();
    $view = $component->render();

    expect($view->name())->toBe('media-library-extensions::components.shared.assets');
});
