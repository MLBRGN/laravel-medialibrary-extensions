<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyTemporaryUploadRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;

class DestroyTemporaryUploadAction
{
    public function __construct(
        public MediaService $mediaService
    ) {}

    public function execute(
        DestroyTemporaryUploadRequest $request,
    ): JsonResponse|RedirectResponse {
        $dataSource = $request->data_source;

        $temporaryUpload = $this->mediaService->findTemporaryUpload(
            $request->input('temporaryUploadId') ?: $request->route('temporaryUploadId'),
            $dataSource
        );

        // Delete the medium
        $temporaryUpload->delete();

        $baseId = (string) $request->input('base_id');

        // Reorder remaining uploads
        $this->reorderAllMedia($request, $dataSource);

        return MediaResponse::success(
            $request,
            $baseId,
            __('medialibrary-extensions::messages.medium_removed')
        );
    }

    protected function reorderAllMedia($request, ?string $dataSource = 'default'): void
    {
        $collections = collect($request->input('collections', []))
            ->filter() // remove empty or null entries
            ->values() // flatten to a simple indexed list
            ->all();

        if (empty($collections)) {
            Log::warning('No valid collections provided for reorderAllMedia.');

            return;
        }

        // Stateless client identity logic
        $clientToken = $request->input('client_token')
            ?? $request->cookie('mle_client_token');

        // Derive instanceId strictly from base_id (do not trust client-sent instance_id)
        $baseId = (string) $request->input('base_id');
        $instanceId = InstanceManager::getInstanceId($baseId);

        $temporaryUploads = TemporaryUpload::query()
            ->forDataSource($dataSource)
            ->forCurrentClient(instanceId: $instanceId, clientToken: $clientToken)
            ->whereIn('collection_name', $collections)
            ->get()
            ->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX));

        $priority = 0;
        $connectionName = app(DataSourceResolver::class)->resolveConnection($dataSource);
        foreach ($temporaryUploads as $temporaryUpload) {
            $temporaryUpload->setCustomProperty('priority', $priority);
            $temporaryUpload->order_column = $priority;
            $temporaryUpload->setConnection($connectionName);
            $temporaryUpload->save();
            $priority++;
        }
    }
}
