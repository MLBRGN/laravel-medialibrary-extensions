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
    ) {}

    public function replaceMedium(Media $oldMedia, ?UploadedFile $newFile = null): Media
    {
        Log::info(sprintf(
            'InteractsWithOriginalMedia - replaceMedium [%d]',
            $oldMedia->getKey()
        ));

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
            // Create replacement media.
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

            // Restore metadata.
            $newMedia->custom_properties = $backup->custom_properties ?? [];
            $newMedia->order_column = $backup->order_column;

            if ($connection) {
                $newMedia->setConnection($connection);
            }

            $newMedia->save();

            // Copy original before removing old media.
            $this->reuseOriginal($backup, $newMedia);

            // Only now remove the old record.
            $oldMedia->delete();

            return $newMedia;
        });
    }
//    public function replaceMedium(Media $oldMedia, ?UploadedFile $newFile = null): Media
//    {
//        Log::info('InteractsWithOriginalMedia - replaceMedium oldMedia id: '.$oldMedia->getKey().' invoked');
//
//        $oldId = $oldMedia->id;
//        $backup = $oldMedia->replicate();
//        $backup->id = $oldId; // only for reference
//        $oldMedia->delete();
//        Log::info('backup id: '.$backup->id);
//
//        $model = $backup->model;
//
//        dump($model);
//        if (! $model) {
//            Log::error('InteractsWithOriginalMedia - no model found for media id: '.$oldId);
//            throw new \Exception('no model found for media id: '.$oldId);
//        }
//        if ($oldMedia->getConnectionName()) {
//            dd('oldMedia->getConnectionName()', $model);
//            $model->setConnection($oldMedia->getConnectionName());
//        }
//        $collection = $backup->collection_name;
//
//        if ($newFile) {
////            Log::info('InteractsWithOriginalMedia - newFile: '.$newFile->getClientOriginalName());
//
//            // Use uploaded file
//            $newMedia = $model->addMedia($newFile)
//                ->toMediaCollection($collection);
//        } else {
////            Log::info('InteractsWithOriginalMedia - no newFile');
//            // Use the old media file
//            $oldPath = $backup->getPath();
//            $newMedia = $model->addMedia($oldPath)
//                ->preservingOriginal()
//                ->toMediaCollection($collection);
//        }
//
//        $newMedia->custom_properties = $backup->custom_properties ?? [];
//        $newMedia->order_column = $backup->order_column;
//        $newMedia->setConnection($oldMedia->getConnectionName());
//        $newMedia->save();
//
//        // Also copy original if needed for 'originals' disk
//        $this->reuseOriginal($backup, $newMedia);
//
//        return $newMedia;
//    }

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

//        Log::info("InteractsWithOriginalMedia - Replaced temporary upload [{$backup->id}] with [{$newUpload->id}] on connection [{$newUpload->getConnectionName()}].");

        return $newUpload;
    }

    protected function reuseOriginal(Media $oldMedia, Media $newMedia): void
    {
        $disk = Storage::disk(config('medialibrary-extensions.media_disks.originals'));

        $oldPath = "{$oldMedia->id}/{$oldMedia->file_name}";
        $newPath = "{$newMedia->id}/{$newMedia->file_name}";

        if (! $disk->exists($oldPath)) {
            Log::warning("Original not found for media [{$oldMedia->id}].");

            return;
        }

        if ($disk->exists($newPath)) {
            return;
        }

        $disk->copy($oldPath, $newPath);
    }

    protected function ensureGlobalOrder(Media $media): void
    {
//        Log::info('InteractsWithOriginalMedia - ensureGlobalOrder called for media id: '.$media->getKey());

        // Preserve if already set (for replaced or restored media)
        if ($media->hasCustomProperty('global_order')) {
            return;
        }

        // Compute next global order number
        $maxOrder = $this->getMaxGlobalOrder($media->getConnectionName());
        $nextOrder = ((int) $maxOrder) + 1;
        $media->setConnection($media->getConnectionName());
        $media->setCustomProperty('global_order', $nextOrder);
        $media->save();

//        Log::info("InteractsWithOriginalMedia - Assigned global_order={$nextOrder} to media [{$media->id}]");
    }

    /**
     * Helper: safely get max global_order for both MySQL and SQLite.
     */
    private function getMaxGlobalOrder(?string $connection = null): int
    {
        $connection = $connection ?: DB::getDefaultConnection();
        $driver = DB::connection($connection)->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite lacks JSON_UNQUOTE / JSON_EXTRACT
            return (int) Media::on($connection)->get()
                ->map(fn ($m) => (int) $m->getCustomProperty('global_order', 0))
                ->max();
        }

        return (int) Media::on($connection)
            ->selectRaw("MAX(CAST(JSON_UNQUOTE(JSON_EXTRACT(custom_properties, '$.global_order')) AS UNSIGNED)) as max_order")
            ->value('max_order');
    }
}
