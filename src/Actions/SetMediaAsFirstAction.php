<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetMediumAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

class SetMediaAsFirstAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(SetMediumAsFirstRequest $request): JsonResponse|RedirectResponse
    {
        $dataSource = $request->input('data_source');

        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id; // non-xhr needs media-manager-id, xhr relies on initiatorId

        $model = $this->mediaService->findMediaModel($request->model_type, $request->model_id, $dataSource);
        $mediumId = (int) $request->medium_id;
        $targetMedia = $this->mediaService->findMedium($mediumId, $dataSource);

        $collections = $request->array('collections');

        // Flatten all media across the given collections
        $mediaItems = collect($collections)
            ->flatMap(fn ($collection) => $model->getMedia($collection));

        if ($mediaItems->isEmpty()) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('medialibrary-extensions::messages.no_media_collections'),
            );
        }

        // Sort by current priority
        $sorted = $mediaItems->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX));

        // Remove the target medium and reinsert at front
        $reordered = $sorted->reject(fn ($m) => $m->id === $mediumId)->prepend($targetMedia);

        // Assign new priorities sequentially
        $priority = 0;
        foreach ($reordered as $media) {
            $media->setCustomProperty('priority', $priority);
            $media->order_column = $priority;

            // Ensure we use the correct connection if dataSource is provided
            if ($dataSource) {
                $connectionName = app(DataSourceResolver::class)->resolveConnection($dataSource);
                $media->setConnection($connectionName);
            }

            $media->save();

            $priority++;
        }

        return MediaResponse::success(
            $request,
            $initiatorId,
            $mediaManagerId,
            __('medialibrary-extensions::messages.medium_set_as_main')
        );
    }
}
