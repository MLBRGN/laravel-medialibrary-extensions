<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\Traits;

use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class ChecksMediaLimitsTest extends TestCase
{
    public function test_checks_media_limits_for_permanent_files()
    {
        $model = $this->getTestBlogModel();
        $trait = new class
        {
            use ChecksMediaLimits {
                countModelMediaInCollections as public;
                modelHasAnyMedia as public;
            }
        };

        $this->assertEquals(0, $trait->countModelMediaInCollections($model, ['image' => 'images']));

        $this->getMedium('test.jpg', 'images');
        $model = $model->fresh();

        $this->assertEquals(1, $trait->countModelMediaInCollections($model, ['image' => 'images']));
        $this->assertTrue($trait->modelHasAnyMedia($model, ['image' => 'images']));

        $this->assertEquals(0, $trait->countModelMediaInCollections($model, ['video' => 'videos']));
        $this->assertFalse($trait->modelHasAnyMedia($model, ['video' => 'videos']));
    }

    public function test_checks_media_limits_for_temporary_files()
    {
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

        $this->assertEquals(1, $trait->countTemporaryUploadsInCollections(['image' => $collection], $instanceId, $clientToken));
        $this->assertTrue($trait->temporaryUploadsHaveAnyMedia(['image' => $collection], $instanceId, $clientToken));

        $this->assertEquals(0, $trait->countTemporaryUploadsInCollections(['video' => 'other'], $instanceId, $clientToken));
        $this->assertFalse($trait->temporaryUploadsHaveAnyMedia(['video' => 'other'], $instanceId, $clientToken));
    }
}
