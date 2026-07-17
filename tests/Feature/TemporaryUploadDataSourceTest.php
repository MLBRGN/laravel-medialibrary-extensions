<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature;

use Illuminate\Support\Facades\DB;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaPreviewerTemporaryHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Actions\SetTemporaryUploadAsFirstAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetTemporaryUploadAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure;

it('preserves data source during temporary upload reordering and refresh', function () {
    $this->withSession([]);
    // Use explicit test alias understood by DataSourceResolver
    $dataSource = 'test_alt';
    $targetCollection = 'blog-images';
    $baseId = 'test-media-manager';
    $instanceId = InstanceManager::getInstanceId($baseId);
    $connectionName = PackageInfrastructure::connection('test', 'alt');

    // Ensure a usable 'media' disk for this test environment
    config()->set('filesystems.disks.media', [
        'driver' => 'local',
        'root' => storage_path('app'),
        'url' => '/storage',
        'visibility' => 'public',
    ]);

    // 1. Create temporary uploads on the chosen test connection
    $media1 = new TemporaryUpload([
        'disk' => 'media',
        'path' => 'uploads/temp1.jpg',
        'name' => 'temp1',
        'file_name' => 'temp1.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
        'collection_name' => $targetCollection,
        'instance_id' => $instanceId,
        'client_token' => session()->getId(),
        'custom_properties' => ['priority' => 1],
    ]);
    $media1->setConnection($connectionName);
    $media1->save();

    $media2 = new TemporaryUpload([
        'disk' => 'media',
        'path' => 'uploads/temp2.jpg',
        'name' => 'temp2',
        'file_name' => 'temp2.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
        'collection_name' => $targetCollection,
        'instance_id' => $instanceId,
        'client_token' => session()->getId(),
        'custom_properties' => ['priority' => 2],
    ]);
    $media2->setConnection($connectionName);
    $media2->save();

    // Verify they are in the correct database and NOT in the default one
    expect(DB::connection(PackageInfrastructure::connection('test', 'alt'))->table('mle_temporary_uploads')->where('id', $media1->id)->exists())->toBeTrue();
    expect(DB::connection(PackageInfrastructure::connection('test', 'alt'))->table('mle_temporary_uploads')->where('id', $media2->id)->exists())->toBeTrue();

    // In some test setups, the default might be the same as dataSource if not careful,
    // but here we expect them to be separate.
    $defaultConn = config('database.default');
    if ($defaultConn !== $connectionName) {
        expect(DB::connection($defaultConn)->table('mle_temporary_uploads')->where('id', $media1->id)->exists())->toBeFalse();
    }

    // 2. Execute SetTemporaryUploadAsFirstAction with data_source
    $setAsFirstRequest = new SetTemporaryUploadAsFirstRequest([
        'base_id' => $baseId,
        'target_media_collection' => $targetCollection,
        'medium_id' => $media2->id,
        'collections' => [$targetCollection],
        'data_source' => $dataSource,
        'client_token' => session()->getId(),
    ]);
    $setAsFirstRequest->setLaravelSession(app('session')->driver());

    // CRITICAL: We don't set the connection here anymore, the Action should handle it!
    $mediaService = app(MediaService::class);
    $setAsFirstAction = new SetTemporaryUploadAsFirstAction($mediaService);
    $response = $setAsFirstAction->execute($setAsFirstRequest);

    expect($response->status())->toBe(302); // Redirect back

    // Verify priorities were updated in the CORRECT database
    $media2Refresh = (new TemporaryUpload)->setConnection($connectionName)->find($media2->id);
    $media1Refresh = (new TemporaryUpload)->setConnection($connectionName)->find($media1->id);

    expect($media2Refresh->getCustomProperty('priority'))->toBe(0);
    expect($media1Refresh->getCustomProperty('priority'))->toBe(1);

    // 3. Execute GetMediaPreviewerTemporaryHTMLAction with data_source
    $refreshRequest = new GetMediaManagerPreviewerHTMLRequest([
        'data_source' => $dataSource,
        'base_id' => $baseId,
        // do NOT send instance_id here; server derives it from base_id
        'client_token' => session()->getId(),
        'model_type' => 'Mlbrgn\\MediaLibraryExtensions\\Models\\demo\\Alien',
        'collections' => json_encode([$targetCollection]),
        'options' => json_encode([]),
        'temporary_upload_mode' => 'true',
        'selectable' => 'true',
        'multiple' => 'true',
        'disabled' => 'false',
        'readonly' => 'false',
    ]);
    $refreshRequest->setLaravelSession(app('session')->driver());

    $refreshAction = new GetMediaPreviewerTemporaryHTMLAction($mediaService);
    $refreshResponse = $refreshAction->execute($refreshRequest);

    expect($refreshResponse->status())->toBe(200);
    $data = $refreshResponse->getData(true);

    expect($data['success'])->toBeTrue();
    expect($data['mediaCount'])->toBe(2);
    expect($data['dataSource'])->toBe($dataSource);

    // Verify the HTML contains the media from the correct database
    // The media IDs should be present in the HTML
    expect($data['html'])->toContain('temp1.jpg');
    expect($data['html'])->toContain('temp2.jpg');
});
