<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\Traits;

use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class ChecksMediaLimitsTestClass
{
    use ChecksMediaLimits {
        countModelMediaInCollections as public publicCountModelMediaInCollections;
        countTemporaryUploadsInCollections as public publicCountTemporaryUploadsInCollections;
        modelHasAnyMedia as public publicModelHasAnyMedia;
        temporaryUploadsHaveAnyMedia as public publicTemporaryUploadsHaveAnyMedia;
    }
}

it('checks media limits for permanent files', function () {
    $model = $this->getTestBlogModel();
    $trait = new ChecksMediaLimitsTestClass;

    expect($trait->publicCountModelMediaInCollections($model, ['image' => 'images']))->toBe(0);

    $this->getMedium('test.jpg', 'images');
    $model = $model->fresh();

    expect($trait->publicCountModelMediaInCollections($model, ['image' => 'images']))->toBe(1);
    expect($trait->publicModelHasAnyMedia($model, ['image' => 'images']))->toBeTrue();

    expect($trait->publicCountModelMediaInCollections($model, ['video' => 'videos']))->toBe(0);
    expect($trait->publicModelHasAnyMedia($model, ['video' => 'videos']))->toBeFalse();
});

it('checks media limits for temporary files', function () {
    $sessionId = 'test-session';
    $instanceId = 'test-instance';
    $collection = 'test-collection';

    TemporaryUpload::create([
        'session_id' => $sessionId,
        'instance_id' => $instanceId,
        'collection_name' => $collection,
        'disk' => 'public',
        'path' => 'temp/1.png',
        'file_name' => '1.png',
        'name' => '1',
        'mime_type' => 'image/png',
        'size' => 100,
    ]);

    $trait = new ChecksMediaLimitsTestClass;

    expect($trait->publicCountTemporaryUploadsInCollections(['image' => $collection], $instanceId, $sessionId))->toBe(1);
    expect($trait->publicTemporaryUploadsHaveAnyMedia(['image' => $collection], $instanceId, $sessionId))->toBeTrue();

    expect($trait->publicCountTemporaryUploadsInCollections(['video' => 'other'], $instanceId, $sessionId))->toBe(0);
    expect($trait->publicTemporaryUploadsHaveAnyMedia(['video' => 'other'], $instanceId, $sessionId))->toBeFalse();
});
