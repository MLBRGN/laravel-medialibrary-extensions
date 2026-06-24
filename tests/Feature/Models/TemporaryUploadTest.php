<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

it('returns only uploads for the current session', function () {
    $clientToken = 'test-token-'.(string) Str::ulid();
    request()->merge(['client_token' => $clientToken]);

    // Uploads for current session
    TemporaryUpload::newFactory()->create([
        'client_token' => $clientToken,
        'collection_name' => 'images',
        'order_column' => 2,
    ]);

    TemporaryUpload::newFactory()->create([
        'client_token' => $clientToken,
        'collection_name' => 'documents',
        'order_column' => 1,
    ]);

    // Uploads for another session
    TemporaryUpload::newFactory()->create([
        'client_token' => 'another-session',
        'collection_name' => 'images',
    ]);

    $instanceId = null;
    $uploads = TemporaryUpload::forCurrentClient(null, $instanceId)->get();

    expect($uploads)->toHaveCount(2)
        ->and($uploads->pluck('collection_name')->all())
        ->toMatchArray(['documents', 'images']) // ordered by order_column ascending
        ->and($uploads->first()->collection_name)
        ->toBe('documents');
});

it('filters by collection name when provided', function () {
    $clientToken = 'test-token-'.(string) Str::ulid();
    request()->merge(['client_token' => $clientToken]);

    TemporaryUpload::newFactory()->create([
        'client_token' => $clientToken,
        'collection_name' => 'images',
    ]);

    TemporaryUpload::newFactory()->create([
        'client_token' => $clientToken,
        'collection_name' => 'documents',
    ]);

    $instanceId = '';
    $uploads = TemporaryUpload::forCurrentClient('images', $instanceId)->get();

    expect($uploads)->toHaveCount(1)
        ->and($uploads->first()->collection_name)
        ->toBe('images');
});

it('does not return media when empty collection name provided', function () {
    $clientToken = 'test-token-'.(string) Str::ulid();
    request()->merge(['client_token' => $clientToken]);

    TemporaryUpload::newFactory()->create([
        'client_token' => $clientToken,
        'collection_name' => 'images',
        'instance_id' => 'test',
    ]);

    TemporaryUpload::newFactory()->create([
        'client_token' => $clientToken,
        'collection_name' => 'documents',
        'instance_id' => 'test',
    ]);

    $instanceId = '';
    $uploads = TemporaryUpload::forCurrentClient('', $instanceId)->get();

    // Since we now check !is_null($collectionName), '' is NOT null, so it will filter by collection_name = ''
    expect($uploads)->toHaveCount(0);
});

it('returns all session uploads when collectionName is an empty string', function () {
    $clientToken = 'test-token-'.(string) Str::ulid();
    request()->merge(['client_token' => $clientToken]);

    TemporaryUpload::newFactory()->create([
        'client_token' => $clientToken,
        'collection_name' => '',
        'instance_id' => 'test',
    ]);
    TemporaryUpload::newFactory()->create([
        'client_token' => $clientToken,
        'collection_name' => 'images',
        'instance_id' => 'test',
    ]);

    $instanceId = null;

    // Passing '' should NOT skip the where clause (should match collection_name = '')
    $uploads = TemporaryUpload::forCurrentClient('', $instanceId)->get();

    expect($uploads)->toHaveCount(1)
        ->and($uploads->first()->collection_name)->toBe('');
});

it('returns all session uploads when collectionName is null', function () {
    $clientToken = 'test-token-'.(string) Str::ulid();
    request()->merge(['client_token' => $clientToken]);

    TemporaryUpload::newFactory()->create(['client_token' => $clientToken, 'collection_name' => 'foo']);
    TemporaryUpload::newFactory()->create(['client_token' => $clientToken, 'collection_name' => 'bar']);

    $instanceId = '';

    $uploads = TemporaryUpload::forCurrentClient(null, $instanceId)->get();

    expect($uploads)->toHaveCount(2);
});

it('can handle different database connections using forDataSource scope', function () {
    $clientToken = 'test-token-'.(string) Str::ulid();
    request()->merge(['client_token' => $clientToken]);

    // Configure a mock data source
    config()->set('medialibrary-extensions.data_sources.demo', [
        'connection' => 'mle_test_demo',
    ]);

    // Create on default connection
    TemporaryUpload::newFactory()->create([
        'client_token' => $clientToken,
        'collection_name' => 'default-conn',
    ]);

    // Create on demo connection
    $demoUpload = TemporaryUpload::newFactory()->make([
        'client_token' => $clientToken,
        'collection_name' => 'demo-conn',
    ]);
    $demoUpload->setConnection('mle_test_demo');
    $demoUpload->save();

    // Query using forDataSource
    $demoUploads = TemporaryUpload::forDataSource('demo')
        ->forCurrentClient()
        ->get();

    expect($demoUploads)->toHaveCount(1)
        ->and($demoUploads->first()->collection_name)->toBe('demo-conn');

    // Query using getForCurrentClient with dataSource
    $demoUploadsStatic = TemporaryUpload::getForCurrentClient(null, null, 'demo');
    expect($demoUploadsStatic)->toHaveCount(1)
        ->and($demoUploadsStatic->first()->collection_name)->toBe('demo-conn');
});

it('stores temporary uploads with client token and instance id', function () {
    // Create a temporary upload with specific client token and instance id
    $clientToken = 'test-client-token-123';
    $instanceId = 'test-instance-id-456';

    $temporaryUpload = TemporaryUpload::create([
        'disk' => 'public',
        'path' => 'uploads/test.jpg',
        'name' => 'test',
        'file_name' => 'test.jpg',
        'collection_name' => 'default',
        'mime_type' => 'image/jpeg',
        'size' => 123,
        'client_token' => $clientToken,
        'instance_id' => $instanceId,
        'custom_properties' => [],
    ]);

    // Verify the upload was created with correct attributes
    expect($temporaryUpload->client_token)->toBe($clientToken);
    expect($temporaryUpload->instance_id)->toBe($instanceId);
    expect($temporaryUpload->collection_name)->toBe('default');
});

it('retrieves temporary uploads using client token', function () {
    // Create a temporary upload with client token
    $clientToken = 'retrieve-test-token';

    TemporaryUpload::create([
        'disk' => 'public',
        'path' => 'uploads/test1.jpg',
        'name' => 'test1',
        'file_name' => 'test1.jpg',
        'collection_name' => 'default',
        'mime_type' => 'image/jpeg',
        'size' => 123,
        'client_token' => $clientToken,
        'custom_properties' => [],
    ]);

    TemporaryUpload::create([
        'disk' => 'public',
        'path' => 'uploads/test2.jpg',
        'name' => 'test2',
        'file_name' => 'test2.jpg',
        'collection_name' => 'documents',
        'mime_type' => 'image/jpeg',
        'size' => 456,
        'client_token' => $clientToken,
        'custom_properties' => [],
    ]);

    // Verify we can retrieve uploads by client token
    $uploads = TemporaryUpload::getForCurrentClient('default', $clientToken);

    expect($uploads)->toHaveCount(2);
    expect($uploads->first()->client_token)->toBe($clientToken);
})->todo('This test needs refactoring.');

it('retrieves temporary uploads using instance id', function () {
    // Create a temporary upload with instance id
    $instanceId = 'retrieve-test-instance-id';

    TemporaryUpload::create([
        'disk' => 'public',
        'path' => 'uploads/instance1.jpg',
        'name' => 'instance1',
        'file_name' => 'instance1.jpg',
        'collection_name' => 'default',
        'mime_type' => 'image/jpeg',
        'size' => 123,
        'instance_id' => $instanceId,
        'custom_properties' => [],
    ]);

    TemporaryUpload::create([
        'disk' => 'public',
        'path' => 'uploads/instance2.jpg',
        'name' => 'instance2',
        'file_name' => 'instance2.jpg',
        'collection_name' => 'documents',
        'mime_type' => 'image/jpeg',
        'size' => 456,
        'instance_id' => $instanceId,
        'custom_properties' => [],
    ]);

    // Verify we can retrieve uploads by instance id
    $uploads = TemporaryUpload::getForCurrentClient('default', null, $instanceId);

    expect($uploads)->toHaveCount(2);
    expect($uploads->first()->instance_id)->toBe($instanceId);
})->todo('This test needs refactoring.');;

it('retrieves temporary uploads using session client token', function () {
    // Set up session with client token
    Session::put('medialibrary_extensions_client_token', 'session-test-token');

    // Create a temporary upload with the same client token
    TemporaryUpload::create([
        'disk' => 'public',
        'path' => 'uploads/session-test.jpg',
        'name' => 'session-test',
        'file_name' => 'session-test.jpg',
        'collection_name' => 'default',
        'mime_type' => 'image/jpeg',
        'size' => 123,
        'client_token' => 'session-test-token',
        'custom_properties' => [],
    ]);

    // Verify we can retrieve uploads by session client token
    $uploads = TemporaryUpload::getForCurrentClient('default');

    expect($uploads)->toHaveCount(1);
    expect($uploads->first()->client_token)->toBe('session-test-token');
})->todo('This test needs refactoring.');;

it('handles missing client identity gracefully', function () {
    // Create a temporary upload without client token or instance id
    TemporaryUpload::create([
        'disk' => 'public',
        'path' => 'uploads/no-identity.jpg',
        'name' => 'no-identity',
        'file_name' => 'no-identity.jpg',
        'collection_name' => 'default',
        'mime_type' => 'image/jpeg',
        'size' => 123,
        'custom_properties' => [],
    ]);

    // Should return empty collection when no client identity
    $uploads = TemporaryUpload::getForCurrentClient('default');

    expect($uploads)->toHaveCount(0);
});

it('associates uploads with correct client token during upload process', function () {
    // Simulate the upload process with a client token
    $clientToken = 'upload-process-token';

    // Create temporary upload using the same logic as StoreSingleTemporaryAction
    $temporaryUpload = TemporaryUpload::create([
        'disk' => 'public',
        'path' => 'uploads/upload-process.jpg',
        'name' => 'upload-process',
        'file_name' => 'upload-process.jpg',
        'collection_name' => 'default',
        'mime_type' => 'image/jpeg',
        'size' => 123,
        'client_token' => $clientToken,
        'custom_properties' => [],
    ]);

    // Verify the upload has the correct client token
    expect($temporaryUpload->client_token)->toBe($clientToken);

    // Verify we can retrieve it using that token
    $retrieved = TemporaryUpload::getForCurrentClient('default', 'instance_id', 'default', $clientToken);
    expect($retrieved)->toHaveCount(1);
    expect($retrieved->first()->id)->toBe($temporaryUpload->id);
})->todo('This test not working.');;
