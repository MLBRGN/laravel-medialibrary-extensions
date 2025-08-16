<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerDestroyRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DeleteMediumAction
{
    public function execute(MediaManagerDestroyRequest $request, Media $media): JsonResponse|RedirectResponse
    {
        $initiatorId = $request->initiator_id;

        // Delete the medium
        $model = $media->model; // Get the associated model
        $media->delete();

        // Re-sort all media across all collections
        $this->reorderAllMedia($model);

        return MediaResponse::success(
            $request,
            $initiatorId,
            __('media-library-extensions::messages.medium_removed')
        );
    }

    protected function reorderAllMedia($model): void
    {
        // Get all media for the model, sorted by existing priority
        $mediaItems = $model->getMedia()->sortBy(fn($m) => $m->getCustomProperty('priority', PHP_INT_MAX));

        $priority = 0;
        foreach ($mediaItems as $media) {
            $media->setCustomProperty('priority', $priority++);
            $media->save();
        }
    }
}
