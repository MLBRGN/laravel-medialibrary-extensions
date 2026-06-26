<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreUpdatedMediaAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreUpdatedMediaRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

beforeEach(function () {
    //    Storage::fake('public');
    //    config(['medialibrary-extensions.media_disks.temporary' => 'public']);
    //
    //    config(['medialibrary-extensions.data_sources.demo' => [
    //        'connection' => 'mle_test_demo',
    //    ]]);

    // Ensure the connection exists in DB config
    //    config(['database.connections.media_demo' => config('database.connections.testbench')]);
});

it('correctly replaces a temporary upload on a custom data source', function () {
    $baseId = 'initiator-123';
    $baseId = 'media-manager-456';
    $dataSource = 'mle_test_demo';

    // 1. Create an initial temporary upload on the custom connection
    $existingUpload = new TemporaryUpload([
        'disk' => 'public',
        'path' => 'old.jpg',
        'name' => 'old',
        'file_name' => 'old.jpg',
        'collection_name' => 'test',
        'mime_type' => 'image/jpeg',
        'size' => 123,
        'client_token' => session()->getId(),
        'custom_properties' => [],
    ]);
    $existingUpload->setConnection($dataSource);
    $existingUpload->save();
    $oldId = $existingUpload->id;

    // Verify it exists in the custom database
    $check = TemporaryUpload::on($dataSource)->find($oldId);
    if (! $check) {
        // If it's not found, maybe it was saved to the default connection despite setConnection?
        // Let's check testbench.
        $checkDefault = TemporaryUpload::on('mle_test_host_app')->find($oldId);
        if ($checkDefault) {
            throw new Exception("TemporaryUpload was saved to testbench instead of $dataSource");
        }
        throw new Exception('TemporaryUpload not found in either database');
    }

    // 2. Prepare request to update it
    $newFile = UploadedFile::fake()->image('new.jpg');

    $request = StoreUpdatedMediaRequest::create('/mlbrgn-mle/media/update', 'POST', [
        'medium_id' => $oldId,
        'collection' => 'test',
        'collections' => ['image' => 'test'],
        'temporary_upload_mode' => 'true',
        'base_id' => $baseId,
        'base_id' => $baseId,
        'data_source' => $dataSource,
    ], [], ['file' => $newFile]);

    $request->headers->set('Accept', 'application/json');
    $request->setLaravelSession(app('session')->driver());

    // 3. Execute the action
    $action = app(StoreUpdatedMediaAction::class);
    $response = $action->execute($request);

    // 4. Assertions
    expect($response->getStatusCode())->toBe(200);
    $data = $response->getData(true);
    $newId = $data['newMediumId'];

    expect($newId)->not->toBeNull();

    // Check if old one is gone from custom database
    expect(TemporaryUpload::on($dataSource)->find($oldId))->toBeNull();

    // Check if new one exists in custom database
    $newUpload = TemporaryUpload::on($dataSource)->find($newId);
    expect($newUpload)->not->toBeNull();
    expect($newUpload->file_name)->toBe('new.jpg');

    // Check if it's NOT in the default database
    expect(TemporaryUpload::on('mle_test_host_app')->find($newId))->toBeNull();

    // Check if file exists on disk
    Storage::disk('public')->assertExists($newUpload->path);
})->todo('ai generated - refactor this test');
