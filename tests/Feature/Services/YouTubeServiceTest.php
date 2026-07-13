<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;
use Mlbrgn\MediaLibraryExtensions\Services\YouTubeService;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

beforeEach(function () {
    $this->dataSourceResolver = new DataSourceResolver;
//    $this->youTubeThumbnailDownloader
//    $this->service = new YouTubeService($this->dataSourceResolver);

    $this->service = app(YouTubeService::class);

    Storage::fake('media');
    Auth::shouldReceive('id')->andReturn(1);
});

it('uploads a YouTube thumbnail to a HasMedia model', function () {
    $model = $this->getTestBlogModel();

    $youTubeUrl = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
    $media = $this->service->uploadThumbnailFromUrl(
        $model,
        $youTubeUrl,
        'image_collection'
    );

    expect($media)->not()->toBeNull();

    $modelMedia = $model->getMedia('image_collection')->first();
    expect($modelMedia->getCustomProperty('youtube-id'))->toBe('dQw4w9WgXcQ');
    expect($modelMedia->getCustomProperty('youtube-url'))->toBe($youTubeUrl);
});

it('returns null if uploadThumbnailFromUrl fails', function () {
    $model = $this->getTestBlogModel();

    $result = $this->service->uploadThumbnailFromUrl(
        $model,
        'https://www.youtube.com/watch?v=dQw',
        'image_collection'
    );

    expect($result)->toBeNull();
});

it('stores a temporary thumbnail from URL', function () {
    $youtubeUrl = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
    $clientToken = 'fake-session';
    $collection = 'temp-collection';

    $tempUpload = $this->service->storeTemporaryThumbnailFromUrl(
        youtubeUrl: $youtubeUrl,
        clientToken: $clientToken,
        customId: 'dQw4w9WgXcQ',
        collection: $collection
    );

    expect($tempUpload)->toBeInstanceOf(TemporaryUpload::class)
        ->and($tempUpload->collection_name)->toBe($collection)
        ->and($tempUpload->custom_properties['youtube-url'])->toBe($youtubeUrl)
        ->and($tempUpload->custom_properties['youtube-id'])->toBe('dQw4w9WgXcQ');

    Storage::disk(config('medialibrary-extensions.media_disks.temporary'))
        ->assertExists($tempUpload->path);
});

it('returns null when thumbnail URL is invalid', function () {
    $tempUpload = $this->service->storeTemporaryThumbnailFromUrl(
        youtubeUrl: 'https://www.youtube.com/watch?v=invalid',
        clientToken: 'fake-session',
        customId: null
    );

    expect($tempUpload)->toBeNull();
});
