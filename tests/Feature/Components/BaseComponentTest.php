<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Tests\Support\classes\ExtendedBaseComponent;

// TODO
it('initializes with provided id', function () {
    $component = new ExtendedBaseComponent('my-id');

    expect($component->id)->toBe('my-id');
    expect($component->instanceId)->toBe(InstanceManager::getInstanceId('my-id'));
});
