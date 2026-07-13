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
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;

class SetTemporaryUploadAsFirstAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(SetTemporaryUploadAsFirstRequest $request): JsonResponse|RedirectResponse
    {
        $dataSource = $request->input('data_source', 'default');

        $mediumId = (int) $request->medium_id;

        $baseId = (string) $request->input('base_id');

        $collections = $request->array('collections');
        // Derive instanceId strictly from base_id (do not trust client-sent instance_id)
        $instanceId = InstanceManager::getInstanceId($baseId);

        // Build effective collection names: include provided collections and the explicit target collection
        $collectionNames = is_array($collections) ? array_values($collections) : [];
        $targetCollection = (string) $request->input('target_media_collection', '');
        if ($targetCollection !== '') {
            $collectionNames[] = $targetCollection;
        }
        // Normalize and deduplicate
        $collectionNames = array_values(array_unique(array_filter(array_map(function ($name) {
            // Normalize known pluralization edge-case for audio collections ("*-audios" -> "*-audio")
            if (is_string($name) && str_ends_with($name, '-audios')) {
                return substr($name, 0, -1); // drop trailing 's'
            }
            return $name;
        }, $collectionNames))));

        if (empty($collectionNames)) {
            return MediaResponse::error(
                $request,
                $baseId,
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
            Log::warning('SetTemporaryUploadAsFirstAction.no_items_for_scope', [
                'base_id' => $baseId,
                'derived_instance_id' => $instanceId,
                'has_client_token' => (bool) $clientToken,
                'data_source' => $dataSource,
                'collections' => $collectionNames,
            ]);
            return MediaResponse::error(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.no_media_collections'),
            );
        }

        $targetMedia = $this->mediaService->findTemporaryUpload($mediumId, $dataSource);

        if (! $targetMedia) {
            Log::warning('SetTemporaryUploadAsFirstAction.target_not_found', [
                'medium_id' => $mediumId,
                'data_source' => $dataSource,
            ]);
            return MediaResponse::error(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.medium_not_found'),
            );
        }

        // Sort by current priority
        $sorted = $mediaItems->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX));

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
            $baseId,
            __('medialibrary-extensions::messages.medium_set_as_main')
        );
    }
}
