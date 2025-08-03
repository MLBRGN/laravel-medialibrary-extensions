<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SetMediumAsFirstAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(SetAsFirstRequest $request): JsonResponse|RedirectResponse
    {
        $model = $this->mediaService->resolveModel($request->model_type, $request->model_id);
        $initiatorId = $request->initiator_id;
        $collection = $request->target_media_collection;
        $mediumId = (int) $request->medium_id;

        $mediaItems = $model->getMedia($collection);

        if($mediaItems->count() === 0){
            return MediaResponse::error(
                $request,
                $initiatorId,
                __('media-library-extensions::messages.no_media'),
            );
        }

        $orderedIds = $mediaItems->pluck('id')->toArray();
        $orderedIds = array_filter($orderedIds, fn ($id) => $id !== $mediumId);
        array_unshift($orderedIds, $mediumId);

        $this->setMediaOrder($orderedIds);

        return MediaResponse::success(
            $request,
            $initiatorId,
            __('media-library-extensions::messages.medium_set_as_main')
        );
    }

    protected function setMediaOrder(array $orderedIds): void
    {
        Media::setNewOrder($orderedIds);
    }
}
