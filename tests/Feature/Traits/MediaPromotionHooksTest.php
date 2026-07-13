<?php

use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Support\MediaUploadContext;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

beforeEach(function () {
    Storage::fake('media');
    // Ensure the table exists for Blog model (which uses test_posts in TestCase)
});

it('promotes temporary uploads to permanent media on model created event using request', function () {
    $instanceId = 'test-instance-id-request';
    $clientToken = 'test-client-token-request';
    $fileName = 'test-request.jpg';

    // 1. Create a temporary upload
    Storage::disk('media')->put('temp/'.$fileName, 'dummy content');
    $tempUpload = TemporaryUpload::create([
        'disk' => 'media',
        'path' => 'temp/'.$fileName,
        'name' => 'test',
        'file_name' => $fileName,
        'collection_name' => 'images',
        'client_token' => $clientToken,
        'instance_id' => $instanceId,
        'mime_type' => 'image/jpeg',
        'size' => 100,
    ]);

    // 2. Mock request inputs
    request()->merge([
        'instance_id' => $instanceId,
        'client_token' => $clientToken,
    ]);

    // 3. Create the model
    $model = Blog::create(['title' => 'Request Post']);

    // 4. Assert promotion happened
    $model->refresh();
    expect($model->getMedia('images')->count())->toBe(1)
        ->and(TemporaryUpload::count())->toBe(0);
});

it('promotes temporary uploads to permanent media on model created event using context fallback', function () {
    $instanceId = 'test-instance-id';
    $clientToken = 'test-client-token';
    $fileName = 'test.jpg';

    // 1. Create a temporary upload
    Storage::disk('media')->put('temp/'.$fileName, 'dummy content');
    $tempUpload = TemporaryUpload::create([
        'disk' => 'media',
        'path' => 'temp/'.$fileName,
        'name' => 'test',
        'file_name' => $fileName,
        'collection_name' => 'images',
        'client_token' => $clientToken,
        'instance_id' => $instanceId,
        'mime_type' => 'image/jpeg',
        'size' => 100,
    ]);

    expect(TemporaryUpload::count())->toBe(1);

    // 2. Set the MediaUploadContext
    $context = app(MediaUploadContext::class);
    $context->set($instanceId, $clientToken);

    // 3. Create the model - this should trigger the 'created' hook in InteractsWithMediaExtended
    $model = Blog::create(['title' => 'New Post']);

    // 4. Assert promotion happened
    $model->refresh();
    expect($model->getMedia('images')->count())->toBe(1)
        ->and(TemporaryUpload::count())->toBe(0)
        ->and(Storage::disk('media')->exists('temp/'.$fileName))->toBeFalse();

    $media = $model->getFirstMedia('images');
    expect($media->file_name)->toBe($fileName);
});

it('promotes temporary uploads to permanent media on model updated event', function () {
    $instanceId = 'test-instance-id-update';
    $clientToken = 'test-client-token-update';
    $fileName = 'update-test.jpg';

    // 1. Create the model first
    $model = Blog::create(['title' => 'Existing Post']);
    expect($model->getMedia('images')->count())->toBe(0);

    // 2. Create a temporary upload
    Storage::disk('media')->put('temp/'.$fileName, 'dummy content');
    $tempUpload = TemporaryUpload::create([
        'disk' => 'media',
        'path' => 'temp/'.$fileName,
        'name' => 'update-test',
        'file_name' => $fileName,
        'collection_name' => 'images',
        'client_token' => $clientToken,
        'instance_id' => $instanceId,
        'mime_type' => 'image/jpeg',
        'size' => 100,
    ]);

    expect(TemporaryUpload::count())->toBe(1);

    // 3. Set the MediaUploadContext
    $context = app(MediaUploadContext::class);
    $context->set($instanceId, $clientToken);

    // 4. Update the model - this should trigger the 'updated' hook in InteractsWithMediaExtended
    $model->update(['title' => 'Updated Post']);

    // 5. Assert promotion happened
    $model->refresh();
    expect($model->getMedia('images')->count())->toBe(1)
        ->and(TemporaryUpload::count())->toBe(0)
        ->and(Storage::disk('media')->exists('temp/'.$fileName))->toBeFalse();

    $media = $model->getFirstMedia('images');
    expect($media->file_name)->toBe($fileName);
});

it('does not promote when context is missing', function () {
    $instanceId = 'test-instance-id-none';
    $clientToken = 'test-client-token-none';
    $fileName = 'no-promote.jpg';

    // 1. Create a temporary upload
    Storage::disk('media')->put('temp/'.$fileName, 'dummy content');
    $tempUpload = TemporaryUpload::create([
        'disk' => 'media',
        'path' => 'temp/'.$fileName,
        'name' => 'no-promote',
        'file_name' => $fileName,
        'collection_name' => 'images',
        'client_token' => $clientToken,
        'instance_id' => $instanceId,
        'mime_type' => 'image/jpeg',
        'size' => 100,
    ]);

    expect(TemporaryUpload::count())->toBe(1);

    // 2. Ensure context is NOT set (or clear it)
    // MediaUploadContext is a singleton in Laravel container by default if used via app() usually,
    // but here we just don't call set().

    // 3. Create the model
    $model = Blog::create(['title' => 'No Context Post']);

    // 4. Assert NO promotion happened
    $model->refresh();
    expect($model->getMedia('images')->count())->toBe(0)
        ->and(TemporaryUpload::count())->toBe(1);
});
