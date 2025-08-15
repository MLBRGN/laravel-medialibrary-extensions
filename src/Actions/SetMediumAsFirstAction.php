<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Models\Media;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

class SetMediumAsFirstAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(SetAsFirstRequest $request): JsonResponse|RedirectResponse
    {
        $model = $this->mediaService->resolveModel($request->model_type, $request->model_id);
        $initiatorId = $request->initiator_id;
        $mediumId = (int) $request->medium_id;

        $mediaItems = $model->getMedia("*");

        if ($mediaItems->count() === 0) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                __('media-library-extensions::messages.no_media'),
            );
        }

        // Find target media
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

        // Remove the target medium and reinsert at front
        $reordered = $sorted->reject(fn($m) => $m->id === $mediumId)->prepend($targetMedia);

        // Assign new priorities sequentially
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

//    public function execute(SetAsFirstRequest $request): JsonResponse|RedirectResponse
//    {
//        $model = $this->mediaService->resolveModel($request->model_type, $request->model_id);
//        $initiatorId = $request->initiator_id;
//        $collection = $request->target_media_collection;
//        $mediumId = (int) $request->medium_id;
//
//        $mediaItems = $model->getMedia($collection);
//
//        if($mediaItems->count() === 0){
//            return MediaResponse::error(
//                $request,
//                $initiatorId,
//                __('media-library-extensions::messages.no_media'),
//            );
//        }
//
//        $orderedIds = $mediaItems->pluck('id')->toArray();
//        $orderedIds = array_filter($orderedIds, fn ($id) => $id !== $mediumId);
//        array_unshift($orderedIds, $mediumId);
//
//        $this->setMediaOrder($orderedIds);
//
//        return MediaResponse::success(
//            $request,
//            $initiatorId,
//            __('media-library-extensions::messages.medium_set_as_main')
//        );
//    }

    protected function setMediaOrder(array $orderedIds): void
    {
        Media::setNewOrder($orderedIds);
    }
}
