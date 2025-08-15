<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetTemporaryUploadAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

class SetTemporaryUploadAsFirstAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(SetTemporaryUploadAsFirstRequest $request): JsonResponse|RedirectResponse
    {
        $initiatorId = $request->initiator_id;
        $collection = $request->target_media_collection;
        $mediumId = (int) $request->medium_id;

//        $mediaItems = TemporaryUpload::where('session_id', $request->session()->getId())
//            ->when($collection, fn ($query) => $query->where('custom_properties->image_collection', $collection))
//            ->orderBy('order_column')
//            ->get();

        // Pull all temporary uploads for this session (any collection)
        $mediaItems = TemporaryUpload::where('session_id', $request->session()->getId())
            ->get();

        if ($mediaItems->isEmpty()) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                __('media-library-extensions::messages.no_media'),
            );
        }

        // Find the target upload
        $targetMedia = $mediaItems->firstWhere('id', $mediumId);
        if (!$targetMedia) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                __('media-library-extensions::messages.medium_not_found'),
            );
        }
        // Sort by current priority
        $sorted = $mediaItems->sortBy(fn($m) => $m->getCustomProperty('priority', PHP_INT_MAX));

        // Move target to front
        $reordered = $sorted->reject(fn($m) => $m->id === $mediumId)->prepend($targetMedia);

        // Reassign priorities
        $priority = 0;
        foreach ($reordered as $media) {
            $media->setCustomProperty('priority', $priority++);
            $media->save();
        }

        return MediaResponse::success(
            $request,
            $initiatorId,
            __('media-library-extensions::messages.medium_set_as_main')
        );
    }

    protected function setTemporaryUploadOrder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            TemporaryUpload::where('id', $id)->update(['order_column' => $index + 1]);
        }
    }
}
