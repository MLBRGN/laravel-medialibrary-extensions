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
        $modelType = $request->model_type;
        $modelId = $request->model_id;

        $dataSource = $request->input('data_source', 'default');

        $baseId = (string) $request->input('base_id');

        $model = $this->mediaService->resolveModelById($modelType, $modelId, $dataSource);
        $mediumId = (int) $request->medium_id;
        $targetMedia = $this->mediaService->findMedium($mediumId, $dataSource);

        $collections = $request->array('collections');

        // Flatten all media across the given collections
        $mediaItems = collect($collections)
            ->flatMap(fn ($collection) => $model->getMedia($collection));

        if ($mediaItems->isEmpty()) {
            return MediaResponse::error(
                $request,
                $baseId,
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
            $baseId,
            __('medialibrary-extensions::messages.medium_set_as_main')
        );
    }
}
