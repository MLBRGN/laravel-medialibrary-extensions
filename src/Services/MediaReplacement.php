<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

/*
 * Replaces media
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaReplacement
{
    public function __construct(
        private OriginalMediaService $originalMediaService
    ) {}

    // stores an updated medium and removes the old medium
    // this will create a new id for the new medium!
    public function replaceMedium(Media $oldMedia, ?UploadedFile $newFile = null): Media
    {
        $connection = $oldMedia->getConnectionName();

        // Get the owning model before anything changes.
        $model = $oldMedia->model;

        if (! $model) {
            throw new \RuntimeException(
                "No owning model found for media [{$oldMedia->getKey()}]."
            );
        }

        if ($connection) {
            $model->setConnection($connection);
        }

        // Keep a snapshot of the existing media so its metadata can be
        // restored to the replacement after creating the replacement.
        $backup = $oldMedia->replicate();
        $collection = $backup->collection_name;

        return DB::connection($connection)->transaction(function () use (
            $oldMedia,
            $backup,
            $model,
            $collection,
            $newFile,
            $connection
        ) {
            // Create the replacement media from either the uploaded file
            // or the existing media file.
            if ($newFile) {
                $newMedia = $model
                    ->addMedia($newFile)
                    ->toMediaCollection($collection);
            } else {
                $newMedia = $model
                    ->addMedia($backup->getPath())
                    ->preservingOriginal()
                    ->toMediaCollection($collection);
            }

            // Preserve metadata from the original media record.
            $newMedia->custom_properties = $backup->custom_properties ?? [];
            $newMedia->order_column = $backup->order_column;

            // Ensure the replacement media is created using the same database
            // connection as the original media.
            if ($connection) {
                $newMedia->setConnection($connection);
            }

            $newMedia->save();

            // Reuse the archived original before deleting the old media.
            // Overwrite destination to ensure the historical original from the
            // old media becomes the authoritative archived original for the
            // replacement media (lineage correctness).
            $this->originalMediaService->copyArchivedOriginal($oldMedia, $newMedia, overwrite: true);

            // Lineage: prefer earliest known source id if present on the backup
            $sourceId = $backup->getCustomProperty('original_source_media_id')
                ?? $oldMedia->getKey();
            $newMedia->setCustomProperty('original_source_media_id', $sourceId);

            // Normalize original flags/paths on the replacement so the
            // archived original path reflects the new media id.
            $newMedia->setCustomProperty('has_original_copy', true);
            $newMedia->setCustomProperty('original_path', $newMedia->id.'/'.$newMedia->file_name);

            $newMedia->save();

            // Once the replacement has been created successfully, remove
            // the original media record.
            $oldMedia->delete();

            return $newMedia;
        });
    }

    /**
     * Replace an existing TemporaryUpload with a new file.
     * TODO do i need this?
     */
    public function replaceTemporaryUpload(TemporaryUpload $oldUpload, UploadedFile $newFile): TemporaryUpload
    {
        $disk = config('medialibrary-extensions.media_disks.temporary');
        $basePath = '';

        $backup = $oldUpload->replicate(['id']);
        $oldUpload->delete();

        $safeFilename = sanitizeFilename(pathinfo($newFile->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $newFile->getClientOriginalExtension();
        $filename = "{$safeFilename}.{$extension}";
        $directory = "{$basePath}";

        Storage::disk($disk)->putFileAs($directory, $newFile, $filename);

        $newUpload = new TemporaryUpload([
            'disk' => $disk,
            'path' => "{$directory}/{$filename}",
            'name' => $safeFilename,
            'file_name' => $newFile->getClientOriginalName(),
            'collection_name' => $backup->collection_name,
            'mime_type' => $newFile->getMimeType(),
            'size' => $newFile->getSize(),
            'user_id' => $backup->user_id,
            'client_token' => $backup->client_token,
            'order_column' => $backup->order_column ?? 1,
            'custom_properties' => $backup->custom_properties ?? [],
            'instance_id' => $backup->instance_id,
        ]);

        $newUpload->setConnection($oldUpload->getConnectionName());
        $newUpload->save();

        return $newUpload;
    }
}
