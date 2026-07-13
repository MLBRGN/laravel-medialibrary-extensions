<?php

use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

it('checks media limits for permanent files', function () {
    $model = $this->getTestBlogModel();

    $trait = new class
    {
        use ChecksMediaLimits {
            countModelMediaInCollections as public;
            modelHasAnyMedia as public;
        }
    };

    expect($trait->countModelMediaInCollections($model, ['image' => 'images']))
        ->toBe(0);

    $this->getMedium('test.jpg', 'images');

    $model = $model->fresh();

    expect($trait->countModelMediaInCollections($model, ['image' => 'images']))
        ->toBe(1);

    expect($trait->modelHasAnyMedia($model, ['image' => 'images'], 'default'))
        ->toBeTrue();

    expect($trait->countModelMediaInCollections($model, ['video' => 'videos']))
        ->toBe(0);

    expect($trait->modelHasAnyMedia($model, ['video' => 'videos'], 'default'))
        ->toBeFalse();
});

it('checks media limits for temporary files', function () {
    $clientToken = 'test-session';
    $instanceId = 'test-instance';
    $collection = 'test-collection';

    TemporaryUpload::create([
        'client_token' => $clientToken,
        'instance_id' => $instanceId,
        'collection_name' => $collection,
        'disk' => 'public',
        'path' => 'temp/1.png',
        'file_name' => '1.png',
        'name' => '1',
        'mime_type' => 'image/png',
        'size' => 100,
    ]);

    $trait = new class
    {
        use ChecksMediaLimits {
            countTemporaryUploadsInCollections as public;
            temporaryUploadsHaveAnyMedia as public;
        }
    };

    expect($trait->countTemporaryUploadsInCollections(
        ['image' => $collection],
        $instanceId,
        $clientToken
    ))->toBe(1);

    expect($trait->temporaryUploadsHaveAnyMedia(
        ['image' => $collection],
        $instanceId,
        $clientToken,
        'default'
    ))->toBeTrue();

    expect($trait->countTemporaryUploadsInCollections(
        ['video' => 'other'],
        $instanceId,
        $clientToken
    ))->toBe(0);

    expect($trait->temporaryUploadsHaveAnyMedia(
        ['video' => 'other'],
        $instanceId,
        $clientToken,
        'default'
    ))->toBeFalse();
});
