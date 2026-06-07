<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Tests\Support\classes\ExtendedBaseComponent;

// TODO
it('initializes with provided id', function () {
    $component = new ExtendedBaseComponent('my-id');

    expect($component->id)->toBe('my-id');
});

it('generates a unique id if none provided', function () {
    $component = new ExtendedBaseComponent;

    expect($component->id)->toStartWith('component-');
    expect(Str::isUuid(Str::after($component->id, 'component-')))->toBeTrue();
});
