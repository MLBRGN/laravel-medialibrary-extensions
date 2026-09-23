<?php

use Mlbrgn\MediaLibraryExtensions\Services\MediaCounter;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

it('can count effective media', function () {
    config(['medialibrary-extensions.test_client_token' => 'mle-test-token-12345']);

    $mediaCounter = app(MediaCounter::class);
    $collections = ['image_collection'];

    // 1. New model (no media, no temporary uploads)
    $count = $mediaCounter->getEffectiveMediaCount($collections, Blog::class);
    expect($count)->toBe(0);

    // 2. Model with permanent media
    $model = $this->getModelWithMedia(['image' => 2]);
    $count = $mediaCounter->getEffectiveMediaCount($collections, $model);
    expect($count)->toBe(2);

    // 3. Model with permanent media + temporary uploads
    $instanceId = 'test-instance';
    $this->createTemporaryUpload([
        'collection_name' => 'image_collection',
        'instance_id' => $instanceId,
    ]);

    $count = $mediaCounter->getEffectiveMediaCount($collections, $model, $instanceId);
    expect($count)->toBe(3);

    // 4. Model with permanent media + temporary uploads + value (direct upload)
    $count = $mediaCounter->getEffectiveMediaCount($collections, $model, $instanceId, null, 'default', ['file1.jpg']);
    expect($count)->toBe(4);
});

it('handles a null model', function () {
    $mediaCounter = app(MediaCounter::class);
    $collections = ['image_collection'];

    $count = $mediaCounter->getEffectiveMediaCount($collections, null);
    expect($count)->toBe(0);
});

it('ignores numeric values as client-side hints', function () {
    $mediaCounter = app(MediaCounter::class);
    $collections = ['image_collection'];

    // Numeric values should be ignored (they are hints of current count)
    $count = $mediaCounter->getEffectiveMediaCount($collections, null, null, null, 'default', '2');
    expect($count)->toBe(0);

    $count = $mediaCounter->getEffectiveMediaCount($collections, null, null, null, 'default', 1);
    expect($count)->toBe(0);
});

it('counts a single non-numeric value as one', function () {
    $mediaCounter = app(MediaCounter::class);
    $collections = ['image_collection'];

    // Non-numeric string (e.g. from a single file upload in some scenarios)
    $count = $mediaCounter->getEffectiveMediaCount($collections, null, null, null, 'default', 'file.jpg');
    expect($count)->toBe(1);
});

it('aggregates across multiple collections', function () {
    $mediaCounter = app(MediaCounter::class);
    $collections = ['image_collection', 'video_collection'];

    $model = $this->getModelWithMedia([
        'image' => 1,
        'video' => 2,
    ]);

    $count = $mediaCounter->getEffectiveMediaCount($collections, $model);
    expect($count)->toBe(3);
});

it('supports different data sources', function () {
    $mediaCounter = app(MediaCounter::class);
    $collections = ['image_collection'];

    // Use test_alt data source
    $dataSource = 'test_alt';

    // Should not crash and should return 0 for a new model on alt source
    $count = $mediaCounter->getEffectiveMediaCount($collections, Blog::class, null, null, $dataSource);
    expect($count)->toBe(0);
});

it('allows the trait to count effective media', function () {
    config(['medialibrary-extensions.test_client_token' => 'mle-test-token-12345']);

    $class = new class {
        use \Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

        public function getCount($collections, $model, $instanceId)
        {
            return $this->getEffectiveMediaCount($collections, $model, $instanceId);
        }
    };

    $model = $this->getModelWithMedia(['image' => 2]);
    $instanceId = 'test-instance';
    $this->createTemporaryUpload([
        'collection_name' => 'image_collection',
        'instance_id' => $instanceId,
    ]);

    expect($class->getCount(['image_collection'], $model, $instanceId))->toBe(3);
});
