<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\YouTubeService;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

beforeEach(function () {
    $this->service = new YouTubeService();

    Storage::fake('media');
    Auth::shouldReceive('id')->andReturn(1);
});

it('uploads a YouTube thumbnail to a HasMedia model', function () {
    $model = Mockery::mock(HasMedia::class);
    $mediaMock = Mockery::mock(Media::class);

    $model->shouldReceive('addMediaFromUrl')
        ->once()
        ->withArgs(function ($url) {
            return str_contains($url, 'https://img.youtube.com/vi/');
        })
        ->andReturnSelf();

    $model->shouldReceive('usingFileName')->andReturnSelf();
    $model->shouldReceive('withCustomProperties')->andReturnSelf();
    $model->shouldReceive('toMediaCollection')->andReturn($mediaMock);

    $media = $this->service->uploadThumbnailFromUrl(
        $model,
        'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'image_collection'
    );

    expect($media)->toBe($mediaMock);
});

it('returns null if uploadThumbnailFromUrl fails', function () {
    $model = Mockery::mock(HasMedia::class);

    $model->shouldReceive('addMediaFromUrl')->andThrow(new Exception('fail'));

    $result = $this->service->uploadThumbnailFromUrl(
        $model,
        'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'image_collection'
    );

    expect($result)->toBeNull();
});

it('stores a temporary thumbnail from URL', function () {
    $youtubeUrl = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
    $sessionId = 'fake-session';
    $collection = 'temp-collection';

    // Mock file_get_contents to return fake content
//    function file_get_contents($url) {
//        return 'fake-image-content';
//    }

    $tempUpload = $this->service->storeTemporaryThumbnailFromUrl(
        youtubeUrl: $youtubeUrl,
        sessionId: $sessionId,
        customId: 'dQw4w9WgXcQ',
        collection: $collection
    );

    expect($tempUpload)->toBeInstanceOf(TemporaryUpload::class)
        ->and($tempUpload->collection_name)->toBe($collection)
        ->and($tempUpload->custom_properties['youtube-url'])->toBe($youtubeUrl)
        ->and($tempUpload->custom_properties['youtube-id'])->toBe('dQw4w9WgXcQ');

    Storage::disk(config('media-library-extensions.temporary_upload_disk'))
        ->assertExists($tempUpload->path);
});

it('returns null when thumbnail URL is invalid', function () {
    $tempUpload = $this->service->storeTemporaryThumbnailFromUrl(
        youtubeUrl: 'https://www.youtube.com/watch?v=invalid',
        sessionId: 'fake-session',
        customId: null
    );

    expect($tempUpload)->toBeNull();
});
