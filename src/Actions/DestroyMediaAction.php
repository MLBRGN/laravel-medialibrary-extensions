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
        $dataSource = $request->data_source;

        $media = $this->mediaService->findMedium(
            $request->input('mediaId') ?: $request->route('mediaId'),
            $dataSource
        );

        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id; // non-xhr needs media-manager-id, xhr relies on initiatorId

        // Delete the medium
        $model = $media->model;
        $media->delete();

        // Reorder remaining uploads
        $this->reorderAllMedia($request, $model, $dataSource);

        return MediaResponse::success(
            $request,
            $initiatorId,
            $mediaManagerId,
            __('medialibrary-extensions::messages.medium_removed')
        );
    }

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
//            Log::info(sprintf(
//                'Media #%d (%s): old priority=%s → new priority=%d',
//                $media->id,
//                $media->file_name,
//                $media->getCustomProperty('priority'),
//                $priority
//            ));

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
