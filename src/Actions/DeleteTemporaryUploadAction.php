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
    public function execute(
        MediaManagerTemporaryUploadDestroyRequest $request,
        TemporaryUpload $temporaryUpload
    ): JsonResponse|RedirectResponse {
        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id;

        // Collect all possible collections
        $collections = collect([
            $request->input('image_collection'),
            $request->input('document_collection'),
            $request->input('youtube_collection'),
            $request->input('video_collection'),
            $request->input('audio_collection'),
        ])->filter()->all();

        // Return error if no collections provided
        if (empty($collections)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.no_media_collections')
            );
        }

        // Delete the temporary upload
        $temporaryUpload->delete();

        // Reorder remaining uploads
        $this->reorderAllMedia($collections);

        return MediaResponse::success(
            $request,
            $initiatorId,
            $mediaManagerId,
            __('media-library-extensions::messages.medium_removed')
        );
    }

    protected function reorderAllMedia(array $collections): void
    {

        // For testing purposes use session id from header, otherwise real session
        $sessionId = request()->header('X-Test-Session-Id') ?? session()->getId();;

        $temporaryUploads = TemporaryUpload::where('session_id', $sessionId)
            ->whereIn('collection_name', $collections)
            ->get()
            ->sortBy(fn($m) => $m->getCustomProperty('priority', PHP_INT_MAX));

        $priority = 0;
        foreach ($temporaryUploads as $temporaryUpload) {
            $temporaryUpload->setCustomProperty('priority', $priority++);
            $temporaryUpload->save();
        }
    }
}


