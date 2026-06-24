<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Tests\Support\classes\ExtendedBaseComponent;

// TODO
it('initializes with provided id', function () {
    $component = new ExtendedBaseComponent('my-id');

    expect($component->id)->toBe('my-id');
    expect($component->id)->toBe('my-id');
    expect($component->instanceId)->toBeString();
});

it('generates a unique ULID id if none provided', function () {
    $component = new ExtendedBaseComponent;

    expect(Str::isUlid($component->id))->toBeTrue();
    expect($component->id)->toBe($component->id);
    expect($component->instanceId)->toBeString();
});
