<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

/*
 * Replaces media
 */

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
        $stream = fopen($media->getPath(), 'rb');// rb = read and binary

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
            fclose($stream);// always close the stream
        }
    }

    public function copyArchivedOriginal(Media $oldMedia, Media $newMedia): void
    {

        Log::info("Copying archived original from media [{$oldMedia->id}] to media [{$newMedia->id}]");
//        Log::info("Old media: ", $oldMedia->toArray());

        // Use the configured originals disk.
        $disk = Storage::disk(config('medialibrary-extensions.media_disks.originals'));

        // Build the source and destination paths.
        $sourcePath = "{$oldMedia->id}/{$oldMedia->file_name}";
        $destinationPath = "{$newMedia->id}/{$newMedia->file_name}";

        Log::info("Copying archived original from [$sourcePath] to [$destinationPath]");

        // Nothing to reuse if the original has not been archived.
        if (! $disk->exists($sourcePath)) {
            Log::warning("Original not found for media [{$oldMedia->id}].");
            //throw new \RuntimeException("Original not found for media [{$oldMedia->id}].");
            return;
        }

        // Don't overwrite an existing archived original.
        if ($disk->exists($destinationPath)) {
            Log::info("Original already exists for media [{$newMedia->id}].");
            return;
        }

        // Copy the archived original to the replacement media.
        $copied = $disk->copy($sourcePath, $destinationPath);

        Log::info("Original media [{$oldMedia->id}] copied to media [{$newMedia->id}].");
        if (! $copied) {
            Log::warning("Failed to reuse original media [{$oldMedia->id}] for media [{$newMedia->id}].");
            throw new \RuntimeException(
                "Failed to reuse original media [{$oldMedia->id}] for media [{$newMedia->id}]."
            );
        }
    }

}
