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

        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id; // non-xhr needs media-manager-id, xhr relies on initiatorId

        // Reorder remaining uploads
        $this->reorderAllMedia($request, $dataSource);

        return MediaResponse::success(
            $request,
            $initiatorId,
            $mediaManagerId,
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
        $instanceId = $request->input('instance_id');

        $temporaryUploads = TemporaryUpload::query()
            ->forDataSource($dataSource)
            ->when($instanceId, fn ($q) => $q->where('instance_id', $instanceId))
            ->where('client_token', $clientToken)
            ->whereIn('collection_name', $collections)
            ->get()
            ->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX));

        $priority = 0;
        foreach ($temporaryUploads as $temporaryUpload) {
            $temporaryUpload->setCustomProperty('priority', $priority++);

            if ($dataSource) {
                $connectionName = app(DataSourceResolver::class)->resolveConnection($dataSource);
                $temporaryUpload->setConnection($connectionName);
            }

            $temporaryUpload->save();
        }
    }
}
