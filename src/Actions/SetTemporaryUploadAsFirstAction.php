<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetTemporaryUploadAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

class SetTemporaryUploadAsFirstAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(SetTemporaryUploadAsFirstRequest $request): JsonResponse|RedirectResponse
    {
        $dataSource = $request->input('data_source');

        $mediumId = (int) $request->medium_id;

        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id; // non-xhr needs media-manager-id, xhr relies on initiatorId

        $collections = $request->array('collections');
        $instanceId = $request->input('instance_id');

        // Flatten collections array if it's keyed by type
        $collectionNames = is_array($collections) ? array_values($collections) : [];
        $collectionNames = array_filter($collectionNames);

        if (empty($collectionNames)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('medialibrary-extensions::messages.no_media_collections'),
            );
        }

        $clientToken = $request->input('client_token') ?: $request->cookie('mle_client_token');

        $mediaItems = TemporaryUpload::query()
            ->forDataSource($dataSource)
            ->forCurrentClient(instanceId: $instanceId, clientToken: $clientToken)
            ->when(! empty($collectionNames), fn ($query) => $query->whereIn('collection_name', $collectionNames))
            ->get();

        if ($mediaItems->isEmpty()) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('medialibrary-extensions::messages.no_media_collections'),
            );
        }

        $targetMedia = $this->mediaService->findTemporaryUpload($mediumId, $dataSource);

        if (! $targetMedia) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('medialibrary-extensions::messages.medium_not_found'),
            );
        }

        // Sort by current priority
        $sorted = $mediaItems->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX));

        //        foreach ($sorted as $item) {
        //            Log::info('Sorted item', [
        //                'id' => $item->id,
        //            ]);
        //        }

        // Move target to front
        $reordered = $sorted->reject(fn ($m) => (int) $m->id === (int) $mediumId)->prepend($targetMedia);

        // Reassign priorities
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
