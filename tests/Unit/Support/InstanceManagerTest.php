<?php

use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;

it('generates a new instance id', function () {
    $key = 'test-component';

    $id = InstanceManager::getInstanceId($key);

    expect($id)->toBeString();
    expect(Str::isUlid($id))->toBeTrue();
});

it('generates different ids for different calls', function () {
    $id1 = InstanceManager::getInstanceId('key-1');
    $id2 = InstanceManager::getInstanceId('key-2');

    expect($id1)->not->toBe($id2);
});
