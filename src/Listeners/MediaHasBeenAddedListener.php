<?php

namespace Mlbrgn\MediaLibraryExtensions\Listeners;

use Mlbrgn\MediaLibraryExtensions\Services\GlobalOrderService;
use Mlbrgn\MediaLibraryExtensions\Services\OriginalMediaService;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class MediaHasBeenAddedListener
{
    /**
     * Handle the event.
     */
    public function handle(MediaHasBeenAddedEvent $event): void
    {
        $media = $event->media;
//        Log::info('MediaHasBeenAddedListener - handle invoked for media with id: '.$media->getKey());

        $model = $media->model;

        if (! $model) {
            // TODO throw exception?
//            Log::info('MediaHasBeenAddedListener - no model found for media');
        }

        // Skip storing originals if disabled globally or per-model
        if (! $model ||
            (
                method_exists($model, 'shouldStoreOriginals') &&
                ! $model->shouldStoreOriginals()
            )
        ) {
//            Log::info('MediaHasBeenAddedListener - Skipping original copy for model');

            return;
        }

        $originalMediaService = app(OriginalMediaService::class);
        $globalOrderService = app(GlobalOrderService::class);

        // Ensure consistent global order across all media
        $globalOrderService->ensureGlobalOrder($media);

        // Copy original to the originals disk if not already stored
        $originalMediaService->archiveOriginalMedia($media);

        $media->setCustomProperty('original_path', "$media->id/$media->file_name");
        $media->setCustomProperty('has_original_copy', true);
        $media->save();

    }
}
