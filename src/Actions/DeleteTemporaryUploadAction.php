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

        $this->reorderAllMedia();

        return MediaResponse::success($request, $initiatorId, __('media-library-extensions::messages.medium_removed'));
    }

    protected function reorderAllMedia(): void
    {
        // Get all temporary uploads for this session (any collection)
        $mediaItems = TemporaryUpload::where('session_id', session()->getId())
            ->get();

        $sorted = $mediaItems->sortBy(fn($m) => $m->getCustomProperty('priority', PHP_INT_MAX));

        // Reassign priorities
        $priority = 0;
        foreach ($sorted as $medium) {
            $medium->setCustomProperty('priority', $priority++);
            $medium->save();
        }
    }
}
