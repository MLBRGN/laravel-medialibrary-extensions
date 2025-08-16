<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerTemporaryUploadDestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

class DeleteTemporaryUploadAction
{
    public function execute(MediaManagerTemporaryUploadDestroyRequest $request, TemporaryUpload $temporaryUpload): JsonResponse|RedirectResponse
    {
        $initiatorId = $request->initiator_id;
        $temporaryUpload->delete();

        $this->reorderAllMedia($request);

        return MediaResponse::success($request, $initiatorId, __('media-library-extensions::messages.medium_removed'));
    }

    protected function reorderAllMedia($request): void
    {

        $collections = collect([
            $request->input('image_collection'),
            $request->input('document_collection'),
            $request->input('youtube_collection'),
            $request->input('video_collection'),
            $request->input('audio_collection'),
        ])->filter()->all(); // remove falsy values

        // If no collections were given, nothing to do
        if (empty($collections)) {
            return;
        }

        // Get all temporary uploads for this session, but only for the requested collections
        $temporaryUploads = TemporaryUpload::where('session_id', session()->getId())
            ->whereIn('collection_name', $collections)
            ->get()
            ->sortBy(fn($m) => $m->getCustomProperty('priority', PHP_INT_MAX));


        // Reassign priorities
        $priority = 0;
        foreach ($temporaryUploads as $temporaryUpload) {
            $temporaryUpload->setCustomProperty('priority', $priority++);
            $temporaryUpload->save();
        }
    }
}
