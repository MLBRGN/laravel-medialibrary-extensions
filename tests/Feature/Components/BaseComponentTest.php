<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Tests\Support\classes\ExtendedBaseComponent;

// TODO
it('initializes with provided id', function () {
    $component = new ExtendedBaseComponent('my-id');

    expect($component->id)->toBe('my-id');
    expect($component->instanceId)->toBe(\Mlbrgn\MediaLibraryExtensions\Support\InstanceManager::getInstanceId('my-id'));
});
