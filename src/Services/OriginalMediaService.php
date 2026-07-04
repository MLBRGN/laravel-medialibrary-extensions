<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

/*
 * Replaces media
 */

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/*
 * Original media are stored initially by the MediaHasBeenAddedListener.
 */
class OriginalMediaService
{
    public function __construct(
    ) {}

    /**
     * Copy the original media file to the configured 'originals' disk.
     */
    public function archiveOriginalMedia(Media $media): void
    {
        // Use the configured originals disk
        $disk = Storage::disk(config('medialibrary-extensions.media_disks.originals'));

        // Store originals in "<media id>/<filename>".
        $destinationPath = "{$media->id}/{$media->file_name}";

        // check if the original medium already exists
        if ($disk->exists($destinationPath)) {
            return;
        }

        // Stream the file instead of loading it entirely into memory.
        $stream = fopen($media->getPath(), 'rb'); // rb = read and binary

        if (! $stream) {
            throw new RuntimeException(
                "Unable to open media file [{$media->getPath()}]."
            );
        }

        try {
            $copied = $disk->put($destinationPath, $stream);

            if (! $copied) {
                throw new RuntimeException(
                    "Failed to copy original media [{$media->id}] to originals disk."
                );
            }

            $media->setCustomProperty('is_original', true);
            $media->save();
        } finally {
            fclose($stream); // always close the stream
        }
    }

    /**
     * Copy the archived original from the old media id to the new media id.
     *
     * When $overwrite is true, an existing destination will be removed first so the
     * historical original from the old media becomes the authoritative archived
     * original for the replacement media.
     */
    public function copyArchivedOriginal(Media $oldMedia, Media $newMedia, bool $overwrite = false): void
    {

        // Use the configured originals disk.
        $disk = Storage::disk(config('medialibrary-extensions.media_disks.originals'));

        // Build the source and destination paths.
        $sourcePath = "{$oldMedia->id}/{$oldMedia->file_name}";
        $destinationPath = "{$newMedia->id}/{$newMedia->file_name}";

        // Nothing to reuse if the original has not been archived.
        if (! $disk->exists($sourcePath)) {
            Log::warning("Original not found for media [{$oldMedia->id}] at [$sourcePath].");
            // Opportunistic backfill: if the old media file still exists on its disk, try to archive it now
            try {
                $this->archiveOriginalMedia($oldMedia);
            } catch (\Throwable $e) {
                Log::warning("Backfill archiving failed for media [{$oldMedia->id}]: {$e->getMessage()}");
            }

            if (! $disk->exists($sourcePath)) {
                // Still missing, bail out gracefully
                return;
            }
        }

        // Don't overwrite an existing archived original unless explicitly allowed.
        if ($disk->exists($destinationPath)) {
            if (! $overwrite) {
                return;
            }

            $disk->delete($destinationPath);
        }

        // Copy the archived original to the replacement media.
        $copied = $disk->copy($sourcePath, $destinationPath);

        if (! $copied) {
            Log::warning("Failed to reuse original media [{$oldMedia->id}] for media [{$newMedia->id}].");
            throw new RuntimeException(
                "Failed to reuse original media [{$oldMedia->id}] for media [{$newMedia->id}]."
            );
        }
    }
}
