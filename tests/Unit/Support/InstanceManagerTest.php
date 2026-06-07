<?php

use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;

it('generates a new instance id if one does not exist', function () {
    $key = 'test-component';

    // Ensure session is clear
    session()->forget('mle_instances');

    $id = InstanceManager::getInstanceId($key);

    expect($id)->toBeString();
    expect(Str::isUlid($id))->toBeTrue();
    expect(session()->get('mle_instances'))->toHaveKey($key, $id);
});

it('reuses an existing instance id from the session', function () {
    $key = 'test-component';
    $existingId = (string) Str::ulid();

    session()->put('mle_instances', [$key => $existingId]);

    $id = InstanceManager::getInstanceId($key);

    expect($id)->toBe($existingId);
});

it('generates different ids for different keys', function () {
    session()->forget('mle_instances');

    $id1 = InstanceManager::getInstanceId('key-1');
    $id2 = InstanceManager::getInstanceId('key-2');

    expect($id1)->not->toBe($id2);
    expect(session()->get('mle_instances'))->toHaveCount(2);
});
