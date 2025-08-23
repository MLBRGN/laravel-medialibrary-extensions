<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerDestroyRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DeleteMediumAction
{
    public function execute(MediaManagerDestroyRequest $request, Media $media): JsonResponse|RedirectResponse
    {
        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id;// non-xhr needs media-manager-id, xhr relies on initiatorId

        // Delete the medium
        $model = $media->model; // Get the associated model
        $media->delete();

        // Re-sort all media across all collections
        $this->reorderAllMedia($request, $model);

        return MediaResponse::success(
            $request,
            $initiatorId,
            $mediaManagerId,
            __('media-library-extensions::messages.medium_removed')
        );
    }

    protected function reorderAllMedia($request, $model): void
    {
        $collections = collect([
            $request->input('image_collection'),
            $request->input('document_collection'),
            $request->input('youtube_collection'),
            $request->input('video_collection'),
            $request->input('audio_collection'),
        ])->filter()->all(); // remove falsy values

        // Flatten all media from the given collections
        $mediaItems = collect($collections)
            ->flatMap(fn ($collection) => $model->getMedia($collection))
            ->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX));

        $priority = 0;
        foreach ($mediaItems as $media) {
            Log::info(sprintf(
                'Media #%d (%s): old priority=%s → new priority=%d',
                $media->id,
                $media->file_name,
                $media->getCustomProperty('priority'),
                $priority
            ));

            $media->setCustomProperty('priority', $priority);
            $media->save();

            $priority++;
        }
    }
}
