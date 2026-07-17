<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreUpdatedMediaAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreUpdatedMediaRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure;

beforeEach(function () {
    // Fake the default public disk used by temp uploads in tests
    Storage::fake('public');

    // Map the test alt data source to its resolved connection
    $this->altConnection = PackageInfrastructure::connection('test', 'alt');
    config()->set('medialibrary-extensions.data_sources.test_alt.connection', $this->altConnection);

    // Ensure temporary uploads use the public disk in this test
    config()->set('medialibrary-extensions.media_disks.temporary', 'public');
});

it('correctly replaces a temporary upload on a custom data source', function () {
    $baseId = 'media-manager-456';
    $dataSource = 'test_alt';
    $connection = $this->altConnection;

    // 1. Create an initial temporary upload on the custom connection
    // Seed the old file on disk and DB
    Storage::disk('public')->put('old.jpg', 'old-content');

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
    $existingUpload->setConnection($connection);
    $existingUpload->save();
    $oldId = $existingUpload->id;

    // Verify it exists in the custom database
    $check = TemporaryUpload::on($connection)->find($oldId);
    if (! $check) {
        // If it's not found, maybe it was saved to the default connection despite setConnection?
        $checkDefault = TemporaryUpload::on(config('database.default'))->find($oldId);
        if ($checkDefault) {
            throw new Exception("TemporaryUpload was saved to default connection instead of $connection");
        }
        throw new Exception('TemporaryUpload not found in either database');
    }

    // 2. Prepare the request
    $newFile = UploadedFile::fake()->image('new.jpg');

    $request = StoreUpdatedMediaRequest::create('/mlbrgn-mle/media/update', 'POST', [
        'medium_id' => $oldId,
        'collection' => 'test',
        'collections' => ['image' => 'test'],
        'temporary_upload_mode' => 'true',
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
    $newMediumId = $data['newMediumId'];

    expect($newMediumId)->not->toBeNull();

    // Check if the old one is gone from the custom database
    expect(TemporaryUpload::on($connection)->find($oldId))->toBeNull();

    // Check if the new one exists in the custom database
    $newUpload = TemporaryUpload::on($connection)->find($newMediumId);
    expect($newUpload)->not->toBeNull();
    expect($newUpload->file_name)->toBe('new.jpg');

    // Check if it's NOT in the default database
    expect(TemporaryUpload::on(config('database.default'))->find($newMediumId))->toBeNull();

    // Check if the file exists on disk
    Storage::disk('public')->assertExists(ltrim($newUpload->path, '/'));
});
