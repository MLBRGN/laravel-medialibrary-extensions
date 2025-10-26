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

    public function replaceMedium(Media $oldMedia, ?UploadedFile $newFile = null): Media
    {
        Log::info('replaceMedium oldMedia id: ' . $oldMedia->getKey() . ' invoked');

        $oldId = $oldMedia->id;
        $backup = $oldMedia->replicate();
        $backup->id = $oldId; // only for reference
        $oldMedia->delete();
//        $backup = $oldMedia->replicate(['id']);
//        $oldMedia->delete();

//        Log::info('backup: ' . print_r($backup, true));
        Log::info('backup id: ' . $backup->id);

        $model = $backup->model;
        $collection = $backup->collection_name;

        if ($newFile) {
            Log::info('newFile: ' . $newFile->getClientOriginalName());

//            Log::info('newFile: ' . print_r($newFile, true));
            // Use uploaded file
            $newMedia = $model->addMedia($newFile)
                ->toMediaCollection($collection);
        } else {
            Log::info('no newFile');
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
     * TODO do i need this?
     */
    public function replaceTemporaryUpload(TemporaryUpload $oldUpload, UploadedFile $newFile): TemporaryUpload
    {
        Log::info('replaceTemporaryUpload');

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

//        $this->reuseOriginal($backup, $newUpload);

        Log::info("Replaced temporary upload [{$backup->id}] with [{$newUpload->id}].");
        return $newUpload;
    }

    /**
     * Copy the original media file to the configured 'originals' disk.
     */
    protected function copyOriginalMedia(Media $media): void
    {
        Log::info('copyOriginalMedia');

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
        Log::info('reuseOriginal oldMedia id: ' . $oldMedia->getKey() . ' newMedia id: ' . $newMedia->getKey() . ' invoked');

//        Log::info('oldMedia id: ' . $oldMedia->id);
//        Log::info('newMedia id: ' . $newMedia->id);

        $oldPath = "{$oldMedia->id}/{$oldMedia->file_name}";
        $newPath = "{$newMedia->id}/{$newMedia->file_name}";

        if (!Storage::disk('originals')->exists($oldPath)) {
            Log::warning("Old original not found for media [{$oldMedia->id}].");
            return;
        }

        // TODO disabled this code, prevented old original to overwrite new original added by MediaHasBeenAddedListener
//        if (Storage::disk('originals')->exists($newPath)) {
//            Log::info("Original already exists for new media [{$newMedia->id}], skipping reuse.");
//            return;
//        }

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
        Log::info('ensureGlobalOrder');

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
