<?php

namespace Mlbrgn\MediaLibraryExtensions\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class CopyOriginalMediaListener
{
    /**
     * Handle the event.
     */
    public function handle(MediaHasBeenAddedEvent $event): void
    {
        Log::info('handle of CopyOriginalMediaListener invoked');
        $media = $event->media;
        $model = $media->model;

        // Skip if disabled globally or per-model
        if (
            method_exists($model, 'shouldStoreOriginals') &&
            ! $model->shouldStoreOriginals()
        ) {
            return;
        }

        $path = $media->getPath();
        $destination = $media->id . '/' . $media->file_name;

        if (! Storage::disk('originals')->exists($destination)) {
            try {
                Storage::disk('originals')->put($destination, file_get_contents($path));
            } catch (\Throwable $e) {
                Log::error("Failed to copy original media [{$media->id}]: {$e->getMessage()}");
            }
        }


//        $originalPath = $media->getPath();
//        $archivedPath = $media->id . '/' . $media->file_name;
//
//        Storage::disk('originals')->put(
//            $archivedPath,
//            file_get_contents($originalPath)
//        );

    }

}
