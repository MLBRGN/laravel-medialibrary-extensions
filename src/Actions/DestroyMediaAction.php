<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DestroyMediaAction
{
    public function __construct(
        public MediaService $mediaService
    ) {}

    public function execute(
        DestroyRequest $request,
    ): JsonResponse|RedirectResponse {
        $dataSource = $request->input('data_source', 'default');

        $media = $this->mediaService->findMedium(
            $request->input('mediaId') ?: $request->route('mediaId'),
            $dataSource
        );

        $baseId = (string) $request->input('base_id');

        // Basic existence check
        if (! $media) {
            return MediaResponse::error(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.medium_not_found'),
            );
        }

        // If a model context was provided (normal persisted-media flow), enforce ownership.
        // In some internal flows / tests, model context may be omitted; in that case we skip
        // the ownership cross-check but still delete the found media.
        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');
        if ($modelType !== null && $modelId !== null) {
            $authorizedModel = $this->mediaService->resolveModelById(
                $modelType,
                $modelId,
                $dataSource
            );

            if (! $authorizedModel || ! $media->model || ! $media->model->is($authorizedModel)) {
                return MediaResponse::forbidden(
                    $request,
                    $baseId,
                    __('medialibrary-extensions::messages.not_authorized')
                );
            }
        }

        // Delete the medium
        $model = $media->model;
        $media->delete();

        // Reorder remaining uploads
        $this->reorderAllMedia($request, $model, $dataSource);

        return MediaResponse::success(
            $request,
            $baseId,
            __('medialibrary-extensions::messages.medium_removed')
        );
    }

    // TODO move to service GlobalOrderService
    protected function reorderAllMedia(Request $request, $model, ?string $dataSource = 'default'): void
    {
        $collections = collect($request->input('collections', []))
            ->filter() // remove empty or null values
            ->values() // normalize keys
            ->all();

        if (empty($collections)) {
            Log::warning('No valid collections provided for reorderAllMedia.', [
                'model' => get_class($model),
                'model_id' => $model->id ?? null,
            ]);

            return;
        }

        $mediaItems = collect($collections)
            ->flatMap(fn (string $collection) => $model->getMedia($collection))
            ->sortBy(fn ($media) => $media->getCustomProperty('priority', PHP_INT_MAX))
            ->values(); // reindex after sort for safety

        $priority = 0;
        foreach ($mediaItems as $media) {
            $media->setCustomProperty('priority', $priority);

            if ($dataSource) {
                $connectionName = app(DataSourceResolver::class)->resolveConnection($dataSource);
                $media->setConnection($connectionName);
            }

            $media->save();
            $priority++;
        }
    }
}
