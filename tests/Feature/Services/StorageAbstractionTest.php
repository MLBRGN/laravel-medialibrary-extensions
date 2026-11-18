<?php

declare(strict_types=1);

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature\Services;

use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Services\OriginalMediaService;

it('archives media from one disk to another using storage abstraction', function () {
    // Arrange: Configure and fake disks
    config([
        'filesystems.disks.s3_media' => ['driver' => 'local', 'root' => storage_path('framework/testing/disks/s3_media')],
        'filesystems.disks.s3_originals' => ['driver' => 'local', 'root' => storage_path('framework/testing/disks/s3_originals')],
        'medialibrary-extensions.media_disks.originals' => 's3_originals',
    ]);

    Storage::fake('s3_media');
    Storage::fake('s3_originals');

    $model = $this->getTestBlogModel();
    $fileName = 'test.jpg';
    $uploadedFile = $this->getFixtureUploadedFile($fileName);

    // Add media to the 's3_media' disk
    $media = $model->addMedia($uploadedFile)
        ->toMediaCollection('images', 's3_media');

    expect($media->disk)->toBe('s3_media');
    // Verify file exists on the source disk
    expect(Storage::disk('s3_media')->exists($media->id.'/'.$media->file_name))->toBeTrue();

    // Act: Archive the media
    /** @var OriginalMediaService $service */
    $service = app(OriginalMediaService::class);
    $service->archiveOriginalMedia($media);

    // Assert: It should exist on the originals disk
    $destinationPath = $media->id.'/'.$media->file_name;
    expect(Storage::disk('s3_originals')->exists($destinationPath))->toBeTrue();
    expect($media->getCustomProperty('is_original'))->toBeTrue();

    // Clean up
    Storage::disk('s3_media')->delete($media->id.'/'.$media->file_name);
    Storage::disk('s3_originals')->delete($destinationPath);
});

it('replaces media on a specific disk using storage abstraction', function () {
    // Arrange: Configure and fake disks
    config([
        'filesystems.disks.s3_media' => ['driver' => 'local', 'root' => storage_path('framework/testing/disks/s3_media')],
        'filesystems.disks.s3_originals' => ['driver' => 'local', 'root' => storage_path('framework/testing/disks/s3_originals')],
        'medialibrary-extensions.media_disks.originals' => 's3_originals',
    ]);

    Storage::fake('s3_media');
    Storage::fake('s3_originals');

    $model = $this->getTestBlogModel();
    $fileName = 'test.jpg';
    $uploadedFile = $this->getFixtureUploadedFile($fileName);

    // Add initial media to the 's3_media' disk
    $mediaA = $model->addMedia($uploadedFile)
        ->toMediaCollection('images', 's3_media');

    // Archive it (normally happens via listener)
    app(OriginalMediaService::class)->archiveOriginalMedia($mediaA);

    // Act: Replace with existing media (triggering addMediaFromDisk)
    /** @var \Mlbrgn\MediaLibraryExtensions\Services\MediaReplacement $service */
    $service = app(\Mlbrgn\MediaLibraryExtensions\Services\MediaReplacement::class);
    $mediaB = $service->replaceMedium($mediaA); // replace with itself (common for re-processing)

    // Assert: New media created on same disk
    expect($mediaB->disk)->toBe('s3_media');
    expect(Storage::disk('s3_media')->exists($mediaB->id.'/'.$mediaB->file_name))->toBeTrue();
    expect($mediaB->id)->not->toBe($mediaA->id);

    // Assert: Archived original copied to new location on originals disk
    expect(Storage::disk('s3_originals')->exists($mediaB->id.'/'.$mediaB->file_name))->toBeTrue();
});

it('retrieves image info from a non-local disk using storage abstraction', function () {
    // Arrange: Configure and fake disks
    config([
        'filesystems.disks.s3_media' => ['driver' => 'local', 'root' => storage_path('framework/testing/disks/s3_media')],
    ]);

    Storage::fake('s3_media');

    $model = $this->getTestBlogModel();
    $fileName = 'test.jpg';
    $uploadedFile = $this->getFixtureUploadedFile($fileName);

    // Add media to the 's3_media' disk
    $media = $model->addMedia($uploadedFile)
        ->toMediaCollection('images', 's3_media');

    // Act: Get image info
    /** @var \Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog $model */
    $info = $model->getBaseImageInfo($media);

    // Assert
    expect($info['filled'])->toBeTrue();
    expect($info['width'])->toBeGreaterThan(0);
    expect($info['height'])->toBeGreaterThan(0);
});
