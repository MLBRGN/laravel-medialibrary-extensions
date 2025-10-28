<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Illuminate\Support\Facades\Config;
use Mlbrgn\MediaLibraryExtensions\Tests\Support\classes\ExtendedBaseComponent;

// TODO
it('initializes with provided id and theme', function () {
    Config::set('media-library-extensions.frontend_theme', 'default-theme');

    $component = new ExtendedBaseComponent('my-id', 'custom-theme');

    expect($component->id)->toBe('my-id');
});

// TODO
it('generates a unique id if none provided', function () {
    Config::set('media-library-extensions.frontend_theme', 'fallback-theme');

    $component = new ExtendedBaseComponent('my-id', 'another-theme');

    //    expect($component->frontendTheme)->toBe('another-theme');
})->todo();
