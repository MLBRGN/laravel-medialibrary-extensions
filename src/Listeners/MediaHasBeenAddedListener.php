<?php

namespace Mlbrgn\MediaLibraryExtensions\Listeners;

use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOriginalMedia;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class MediaHasBeenAddedListener
{

    use InteractsWithOriginalMedia;

    /**
     * Handle the event.
     */
    public function handle(MediaHasBeenAddedEvent $event): void
    {
        $media = $event->media;
        $model = $media->model;

        Log::info('MediaHasBeenAddedListener invoked for medium: ' . $media->getKey());

        // Skip originals if disabled globally or per-model
        if (
            method_exists($model, 'shouldStoreOriginals') &&
            !$model->shouldStoreOriginals()
        ) {
            Log::info("Skipping original copy for model");
            return;
        }

        // Ensure consistent global order across all media
        $this->ensureGlobalOrder($media);

        // Copy original to the originals disk if not already stored
        $this->copyOriginalMedia($media);

        $media->setCustomProperty('original_path', "$media->id/$media->file_name");
        $media->setCustomProperty('has_original_copy', true);
        $media->save();

    }

}
