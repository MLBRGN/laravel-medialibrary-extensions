<?php

declare(strict_types=1);

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultipleTemporaryAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;

class StoreMultipleTemporaryActionCapTest extends TestCase
{
    protected function makeImage(string $name): UploadedFile
    {
        // 1x1 png
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMB/aywF34AAAAASUVORK5CYII=');
        $path = sys_get_temp_dir().'/'.$name;
        file_put_contents($path, $png);

        return new UploadedFile($path, $name, 'image/png', null, true);
    }

    public function test_it_caps_multiple_temporary_uploads_to_remaining_slots(): void
    {
        config()->set('medialibrary-extensions.max_items_in_shared_media_collections', 10);

        $action = app(StoreMultipleTemporaryAction::class);

        $baseId = 'mmm';
        $instanceId = InstanceManager::getInstanceId($baseId);

        // Seed 8 existing temporary uploads
        for ($i = 0; $i < 8; $i++) {
            TemporaryUpload::query()
                ->forDataSource('default')
                ->create([
                    'collection_name' => 'blog-images-extra',
                    'disk' => config('medialibrary-extensions.media_disks.temporary'),
                    'path' => 'seed-'.$i.'.png',
                    'file_name' => 'seed-'.$i.'.png',
                    'name' => 'seed-'.$i,
                    'mime_type' => 'image/png',
                    'size' => 1,
                    'instance_id' => $instanceId,
                    'client_token' => Str::ulid()->toString(),
                ]);
        }

        // Prepare 5 new files
        $files = [
            $this->makeImage('a1.png'),
            $this->makeImage('a2.png'),
            $this->makeImage('a3.png'),
            $this->makeImage('a4.png'),
            $this->makeImage('a5.png'),
        ];

        $symfony = StoreMultipleRequest::create('/mle/tmp', 'POST', [
            'temporary_upload_mode' => 'true',
            'collections' => ['image' => 'blog-images-extra'],
            'base_id' => $baseId,
            'client_token' => Str::ulid()->toString(),
            'data_source' => 'default',
        ], [], ['media' => $files], [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
        ]);
        $request = StoreMultipleRequest::createFromBase($symfony);

        $response = $action->execute($request);

        $this->assertTrue(method_exists($response, 'getData')); // JsonResponse
        $data = $response->getData(true);
        $this->assertIsArray($data);

        // Only 2 should have been accepted (remaining slots), 3 skipped
        $this->assertStringContainsString('some uploads failed', $data['message'] ?? '');

        // Count in DB should be 10 total for this instance/collection
        $count = TemporaryUpload::query()
            ->forDataSource('default')
            ->where('collection_name', 'blog-images-extra')
            ->where('instance_id', $instanceId)
            ->count();

        $this->assertSame(10, $count);
    }
}
