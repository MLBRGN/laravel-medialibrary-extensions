<?php

declare(strict_types=1);

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature\Services;

use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Services\MediaReplacement;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;

class MediaReplacementTest extends TestCase
{
    public function test_replacement_sets_lineage_and_copies_original_with_overwrite(): void
    {
        // Arrange: create a model and add an initial media (A)
        $model = $this->getTestBlogModel();
        $initialFile = $this->getFixtureUploadedFile('test.png');

        $mediaA = $model->addMedia($initialFile)->toMediaCollection('images');

        // Listener should have archived original to originals disk under A/<file_name>
        $originalsDisk = config('medialibrary-extensions.media_disks.originals');
        $this->assertTrue(
            Storage::disk($originalsDisk)->exists($mediaA->id.'/'.$mediaA->file_name),
            'Original for media A should be archived.'
        );

        // Act: replace A with a new edited upload (B)
        $editedFile = $this->getFixtureUploadedFile('test2.png');
        $service = app(MediaReplacement::class);
        $mediaB = $service->replaceMedium($mediaA, $editedFile);

        // Assert: old media A removed
        $this->assertDatabaseMissing($mediaB->getTable(), ['id' => $mediaA->id]);

        // Assert: lineage and originals metadata on B
        $this->assertSame(
            $mediaA->getKey(),
            $mediaB->getCustomProperty('original_source_media_id'),
            'Replacement should point lineage to original media id.'
        );
        $this->assertTrue($mediaB->getCustomProperty('has_original_copy') === true);
        $this->assertSame($mediaB->id.'/'.$mediaB->file_name, $mediaB->getCustomProperty('original_path'));

        // And the originals disk has a file at B/<file_name> (copied/overwritten from A)
        $this->assertTrue(
            Storage::disk($originalsDisk)->exists($mediaB->id.'/'.$mediaB->file_name),
            'Original for media B should exist after replacement.'
        );
    }
}
