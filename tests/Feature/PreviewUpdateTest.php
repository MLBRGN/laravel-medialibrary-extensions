<?php

use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\View\Components\Preview\MediaPreviews;

it('confirms the MediaPreviews component correctly retrieves temporary uploads when temporaryUploadMode is enabled', function () {
    // 1. Setup client token
    $clientToken = (string) Str::ulid();
    request()->merge(['client_token' => $clientToken]);

    // 2. Create a temporary upload
    $tempUpload = TemporaryUpload::create([
        'disk' => 'public',
        'path' => 'temp/test.jpg',
        'name' => 'test',
        'file_name' => 'test.jpg',
        'collection_name' => 'images',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
        'client_token' => $clientToken,
        'instance_id' => 'test-instance',
    ]);

    // 3. Initialize MediaPreviews with temporaryUploadMode => true
    $component = new MediaPreviews(
        id: 'test-id',
        mediaManagerId: 'test-id',
        modelOrClassName: Blog::class,
        collections: ['image' => 'images'],
        options: ['temporaryUploadMode' => true],
        instanceId: 'test-instance'
    );

    // 4. Assert that the media collection contains the temporary upload
    expect($component->media)->toHaveCount(1);
    expect($component->media->first()->id)->toBe($tempUpload->id);
    expect($component->media->first())->toBeInstanceOf(TemporaryUpload::class);
})->todo('fix this test');

it('correctly handles the flatMap logic in MediaPreviews for temporary uploads', function () {
    $clientToken = (string) Str::ulid();
    request()->merge(['client_token' => $clientToken]);

    // Create multiple temporary uploads in different collections
    TemporaryUpload::create([
        'disk' => 'public', 'path' => 'temp/1.jpg', 'name' => '1', 'file_name' => '1.jpg',
        'collection_name' => 'images', 'mime_type' => 'image/jpeg', 'size' => 100,
        'client_token' => $clientToken, 'instance_id' => 'inst-1',
    ]);

    TemporaryUpload::create([
        'disk' => 'public', 'path' => 'temp/2.pdf', 'name' => '2', 'file_name' => '2.pdf',
        'collection_name' => 'documents', 'mime_type' => 'application/pdf', 'size' => 200,
        'client_token' => $clientToken, 'instance_id' => 'inst-1',
    ]);

    $component = new MediaPreviews(
        id: 'test-id-2',
        mediaManagerId: 'test-id-2',
        modelOrClassName: Blog::class,
        collections: ['image' => 'images', 'document' => 'documents'],
        options: ['temporaryUploadMode' => true],
        instanceId: 'inst-1'
    );

    expect($component->media)->toHaveCount(2);
    $collectionNames = $component->media->pluck('collection_name')->toArray();
    expect($collectionNames)->toContain('images');
    expect($collectionNames)->toContain('documents');
})->todo('fix this test');

it('does not retrieve temporary uploads from other clients', function () {
    $clientToken = (string) Str::ulid();
    request()->merge(['client_token' => $clientToken]);

    TemporaryUpload::create([
        'disk' => 'public', 'path' => 'temp/a.jpg', 'name' => 'a', 'file_name' => 'a.jpg',
        'collection_name' => 'images', 'mime_type' => 'image/jpeg', 'size' => 100,
        'client_token' => 'client-b', // Different client
        'instance_id' => 'inst-1',
    ]);

    $component = new MediaPreviews(
        id: 'test-id-3',
        mediaManagerId: 'test-id-3',
        modelOrClassName: Blog::class,
        collections: ['image' => 'images'],
        options: ['temporaryUploadMode' => true],
        instanceId: 'inst-1'
    );

    expect($component->media)->toHaveCount(0);
});
