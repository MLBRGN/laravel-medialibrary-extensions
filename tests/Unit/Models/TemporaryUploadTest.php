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

    $uploads = TemporaryUpload::forCurrentSession();

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

    $uploads = TemporaryUpload::forCurrentSession('images');

    expect($uploads)->toHaveCount(1)
        ->and($uploads->first()->collection_name)
        ->toBe('images');
});

it('does not return media when empty collection name provided', function () {
    $sessionId = Session::getId();

    TemporaryUpload::newFactory()->create([
        'session_id' => $sessionId,
        'collection_name' => 'images',
    ]);

    TemporaryUpload::newFactory()->create([
        'session_id' => $sessionId,
        'collection_name' => 'documents',
    ]);

    $uploads = TemporaryUpload::forCurrentSession('');

    expect($uploads)->toHaveCount(0);
});

it('returns all session uploads when collectionName is an empty string', function () {
    $sessionId = Session::getId();

    TemporaryUpload::newFactory()->create(['session_id' => $sessionId, 'collection_name' => '']);
    TemporaryUpload::newFactory()->create(['session_id' => $sessionId, 'collection_name' => 'images']);

    // Passing '' should NOT skip the where clause (should match collection_name = '')
    $uploads = TemporaryUpload::forCurrentSession('');

    expect($uploads)->toHaveCount(1)
        ->and($uploads->first()->collection_name)->toBe('');
});

it('returns all session uploads when collectionName is null', function () {
    $sessionId = Session::getId();

    TemporaryUpload::newFactory()->create(['session_id' => $sessionId, 'collection_name' => 'foo']);
    TemporaryUpload::newFactory()->create(['session_id' => $sessionId, 'collection_name' => 'bar']);

    $uploads = TemporaryUpload::forCurrentSession(null);

    expect($uploads)->toHaveCount(2);
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
//    expect(TemporaryUpload::class)->toHaveMethod('forCurrentSession');
// })->skip();
//
// it('determines if the upload is an image', function () {
//    // Arrange
//    $temporaryUpload = new TemporaryUpload();
//    $temporaryUpload->mime_type = 'image/jpeg';
//
//    // Act & Assert
//    expect($temporaryUpload->isImage())->toBeTrue();
// })->skip();
//
// it('determines if the upload is a document', function () {
//    // Arrange
//    $temporaryUpload = new TemporaryUpload();
//    $temporaryUpload->mime_type = 'application/pdf';
//
//    // Act & Assert
//    expect($temporaryUpload->isDocument())->toBeTrue();
// })->skip();
//
// it('determines if the upload is a YouTube video', function () {
//    // Arrange
//    $temporaryUpload = new TemporaryUpload();
//    $temporaryUpload->custom_properties = ['youtube-id' => 'abc123'];
//
//    // Act & Assert
//    expect($temporaryUpload->isYouTubeVideo())->toBeTrue();
// })->skip();
