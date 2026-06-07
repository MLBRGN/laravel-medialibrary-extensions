<?php

use Illuminate\Support\Facades\Session;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

it('returns only uploads for the current session', function () {
    $sessionId = Session::getId();

    // Uploads for current session
    TemporaryUpload::newFactory()->create([
        'session_id' => $sessionId,
        'collection_name' => 'images',
        'order_column' => 2,
    ]);

    TemporaryUpload::newFactory()->create([
        'session_id' => $sessionId,
        'collection_name' => 'documents',
        'order_column' => 1,
    ]);

    // Uploads for another session
    TemporaryUpload::newFactory()->create([
        'session_id' => 'another-session',
        'collection_name' => 'images',
    ]);

    $instanceId = null;
    $uploads = TemporaryUpload::forCurrentSession(null, $instanceId)->get();

    expect($uploads)->toHaveCount(2)
        ->and($uploads->pluck('collection_name')->all())
        ->toMatchArray(['documents', 'images']) // ordered by order_column ascending
        ->and($uploads->first()->collection_name)
        ->toBe('documents');
});

it('filters by collection name when provided', function () {
    $sessionId = Session::getId();

    TemporaryUpload::newFactory()->create([
        'session_id' => $sessionId,
        'collection_name' => 'images',
    ]);

    TemporaryUpload::newFactory()->create([
        'session_id' => $sessionId,
        'collection_name' => 'documents',
    ]);

    $instanceId = '';
    $uploads = TemporaryUpload::forCurrentSession('images', $instanceId)->get();

    expect($uploads)->toHaveCount(1)
        ->and($uploads->first()->collection_name)
        ->toBe('images');
});

it('does not return media when empty collection name provided', function () {
    $sessionId = Session::getId();

    TemporaryUpload::newFactory()->create([
        'session_id' => $sessionId,
        'collection_name' => 'images',
        'instance_id' => 'test',
    ]);

    TemporaryUpload::newFactory()->create([
        'session_id' => $sessionId,
        'collection_name' => 'documents',
        'instance_id' => 'test',
    ]);

    $instanceId = '';
    $uploads = TemporaryUpload::forCurrentSession('', $instanceId)->get();

    // Since we now check !is_null($collectionName), '' is NOT null, so it will filter by collection_name = ''
    expect($uploads)->toHaveCount(0);
});

it('returns all session uploads when collectionName is an empty string', function () {
    $sessionId = Session::getId();

    TemporaryUpload::newFactory()->create([
        'session_id' => $sessionId,
        'collection_name' => '',
        'instance_id' => 'test',
    ]);
    TemporaryUpload::newFactory()->create([
        'session_id' => $sessionId,
        'collection_name' => 'images',
        'instance_id' => 'test',
    ]);

    $instanceId = null;

    // Passing '' should NOT skip the where clause (should match collection_name = '')
    $uploads = TemporaryUpload::forCurrentSession('', $instanceId)->get();

    expect($uploads)->toHaveCount(1)
        ->and($uploads->first()->collection_name)->toBe('');
});

it('returns all session uploads when collectionName is null', function () {
    $sessionId = Session::getId();

    TemporaryUpload::newFactory()->create(['session_id' => $sessionId, 'collection_name' => 'foo']);
    TemporaryUpload::newFactory()->create(['session_id' => $sessionId, 'collection_name' => 'bar']);

    $instanceId = '';

    $uploads = TemporaryUpload::forCurrentSession(null, $instanceId)->get();

    expect($uploads)->toHaveCount(2);
});

it('can handle different database connections using forDataSource scope', function () {
    $sessionId = Session::getId();

    // Configure a mock data source
    config()->set('medialibrary-extensions.data_sources.demo', [
        'connection' => 'media_demo',
    ]);

    // Create on default connection
    TemporaryUpload::newFactory()->create([
        'session_id' => $sessionId,
        'collection_name' => 'default-conn',
    ]);

    // Create on 'media_demo' connection
    $demoUpload = TemporaryUpload::newFactory()->make([
        'session_id' => $sessionId,
        'collection_name' => 'demo-conn',
    ]);
    $demoUpload->setConnection('media_demo');
    $demoUpload->save();

    // Query using forDataSource
    $demoUploads = TemporaryUpload::forDataSource('demo')
        ->forCurrentSession()
        ->get();

    expect($demoUploads)->toHaveCount(1)
        ->and($demoUploads->first()->collection_name)->toBe('demo-conn');

    // Query using getForCurrentSession with dataSource
    $demoUploadsStatic = TemporaryUpload::getForCurrentSession(null, null, 'demo');
    expect($demoUploadsStatic)->toHaveCount(1)
        ->and($demoUploadsStatic->first()->collection_name)->toBe('demo-conn');
});

// it('checks if the isAvailable static method exists', function () {
//    // Call the static method
//    $exists = TemporaryUpload::isAvailable();
//
//    expect($exists)->toBeTrue();
// });

// it('retrieves uploads for the current session', function () {
//    // This test would require a more complex setup with a real database connection
//    // For now, we'll just test that the method exists
//    expect(TemporaryUpload::class)->toHaveMethod('forCurrentSession', $instanceId);
// });
//
// it('determines if the upload is an image', function () {
//    // Arrange
//    $temporaryUpload = new TemporaryUpload();
//    $temporaryUpload->mime_type = 'image/jpeg';
//
//    // Act & Assert
//    expect($temporaryUpload->isImage())->toBeTrue();
// });
//
// it('determines if the upload is a document', function () {
//    // Arrange
//    $temporaryUpload = new TemporaryUpload();
//    $temporaryUpload->mime_type = 'application/pdf';
//
//    // Act & Assert
//    expect($temporaryUpload->isDocument())->toBeTrue();
// });
//
// it('determines if the upload is a YouTube video', function () {
//    // Arrange
//    $temporaryUpload = new TemporaryUpload();
//    $temporaryUpload->custom_properties = ['youtube-id' => 'abc123'];
//
//    // Act & Assert
//    expect($temporaryUpload->isYouTubeVideo())->toBeTrue();
// });
