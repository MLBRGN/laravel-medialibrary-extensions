<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

trait InteractsWithOriginalMedia
{
    /**
     * Replace an existing permanent medium with a new file.
     * Preserves order, priority, and custom properties (including global_order).
     */
//    public function replaceMedium(Media $oldMedia, UploadedFile $newFile): Media
//    {
//        // Backup metadata before delete
//        $backup = $oldMedia->replicate(['id']);
//        $custom = $backup->custom_properties ?? [];
//        $oldMedia->delete();
//
//        $model = $backup->model;
//        $collection = $backup->collection_name;
//
//        // Create new medium
//        $newMedia = $model
//            ->addMedia($newFile)
//            ->toMediaCollection($collection);
//
//        // Restore metadata, including global_order
//        $newMedia->custom_properties = $custom;
//        $newMedia->order_column = $backup->order_column;
//        $newMedia->save();
//
//        // Try to reuse original file
//        $this->reuseOriginal($backup, $newMedia);
//
//        Log::info("Replaced media [{$backup->id}] with [{$newMedia->id}] while preserving global_order.");
//        return $newMedia;
//    }

    public function replaceMedium(Media $oldMedia, ?UploadedFile $newFile = null): Media
    {
        $backup = $oldMedia->replicate(['id']);
        $oldMedia->delete();

        $model = $backup->model;
        $collection = $backup->collection_name;

        if ($newFile) {
            // Use uploaded file
            $newMedia = $model->addMedia($newFile)
                ->toMediaCollection($collection);
        } else {
            // Use the old media file
            $oldPath = $backup->getPath();
            $newMedia = $model->addMedia($oldPath)
                ->preservingOriginal()
                ->toMediaCollection($collection);
        }

        $newMedia->custom_properties = $backup->custom_properties ?? [];
        $newMedia->order_column = $backup->order_column;
        $newMedia->save();

        // Also copy original if needed for 'originals' disk
        $this->reuseOriginal($backup, $newMedia);

        return $newMedia;
    }

    /**
     * Replace an existing TemporaryUpload with a new file.
     */
    public function replaceTemporaryUpload(TemporaryUpload $oldUpload, UploadedFile $newFile): TemporaryUpload
    {
        $disk = config('media-library-extensions.temporary_upload_disk');
        $basePath = config('media-library-extensions.temporary_upload_path');

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
            'session_id' => $backup->session_id,
            'order_column' => $backup->order_column ?? 1,
            'custom_properties' => $backup->custom_properties ?? [],
        ]);

        $newUpload->save();

        $this->reuseOriginal($backup, $newUpload);

        Log::info("Replaced temporary upload [{$backup->id}] with [{$newUpload->id}].");
        return $newUpload;
    }

    /**
     * Copy the original media file to the configured 'originals' disk.
     */
    protected function copyOriginalMedia(Media $media): void
    {
        $path = $media->getPath();
        $destination = "{$media->id}/{$media->file_name}";

        if (Storage::disk('originals')->exists($destination)) {
            Log::info("Original already exists for media [{$media->id}], skipping copy.");
            return;
        }

        try {
            Storage::disk('originals')->put($destination, file_get_contents($path));
            Log::info("Copied original media [{$media->id}] to originals disk.");
        } catch (\Throwable $e) {
            Log::error("Failed to copy original media [{$media->id}]: {$e->getMessage()}");
        }
    }

    /**
     * Reuse an existing original when a medium is replaced.
     */
    protected function reuseOriginal(Media $oldMedia, Media $newMedia): void
    {
        $oldPath = "{$oldMedia->id}/{$oldMedia->file_name}";
        $newPath = "{$newMedia->id}/{$newMedia->file_name}";

        if (!Storage::disk('originals')->exists($oldPath)) {
            Log::warning("Old original not found for media [{$oldMedia->id}].");
            return;
        }

        if (Storage::disk('originals')->exists($newPath)) {
            Log::info("Original already exists for new media [{$newMedia->id}], skipping reuse.");
            return;
        }

        try {
            Storage::disk('originals')->copy($oldPath, $newPath);
            Log::info("Reused old original for new media [{$newMedia->id}].");
        } catch (\Throwable $e) {
            Log::error("Failed to reuse original media: {$e->getMessage()}");
        }
    }

    /**
     * Assign or preserve a global sequential order across all media.
     */
    protected function ensureGlobalOrder(Media $media): void
    {
        // Preserve if already set (for replaced or restored media)
        if ($media->hasCustomProperty('global_order')) {
            return;
        }

        // Compute next global order number
        $maxOrder = Media::query()
            ->selectRaw("MAX(CAST(JSON_UNQUOTE(JSON_EXTRACT(custom_properties, '$.global_order')) AS UNSIGNED)) as max_order")
            ->value('max_order');

        $nextOrder = ((int) $maxOrder) + 1;
        $media->setCustomProperty('global_order', $nextOrder);
        $media->save();

        Log::info("Assigned global_order={$nextOrder} to media [{$media->id}]");
    }
}
