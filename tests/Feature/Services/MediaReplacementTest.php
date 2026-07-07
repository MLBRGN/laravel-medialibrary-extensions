<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Services\MediaReplacement;

it('sets lineage and copies original with overwrite during replacement', function () {
    // Arrange: create a model and add an initial media (A)
    $model = $this->getTestBlogModel();
    $initialFile = $this->getFixtureUploadedFile('test.png');

    $mediaA = $model->addMedia($initialFile)->toMediaCollection('images');

    // Listener should have archived original to originals disk under A/<file_name>
    $originalsDisk = config('medialibrary-extensions.media_disks.originals');

    expect(
        Storage::disk($originalsDisk)->exists($mediaA->id.'/'.$mediaA->file_name)
    )->toBeTrue();

    // Act: replace A with a new edited upload (B)
    $editedFile = $this->getFixtureUploadedFile('test2.png');

    $service = app(MediaReplacement::class);
    $mediaB = $service->replaceMedium($mediaA, $editedFile);

    // Assert: old media A removed
    $this->assertDatabaseMissing($mediaB->getTable(), [
        'id' => $mediaA->id,
    ]);

    // Assert: lineage and originals metadata on B
    expect($mediaB->getCustomProperty('original_source_media_id'))
        ->toBe($mediaA->getKey());

    expect($mediaB->getCustomProperty('has_original_copy'))
        ->toBeTrue();

    expect($mediaB->getCustomProperty('original_path'))
        ->toBe($mediaB->id.'/'.$mediaB->file_name);

    // Assert: originals disk has a file at B/<file_name>
    expect(
        Storage::disk($originalsDisk)->exists($mediaB->id.'/'.$mediaB->file_name)
    )->toBeTrue();
});
