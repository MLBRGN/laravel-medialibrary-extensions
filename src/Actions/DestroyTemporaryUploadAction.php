<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyTemporaryUploadRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

class DestroyTemporaryUploadAction
{
    public function __construct(
        public MediaService $mediaService
    ) {}

    public function execute(
        DestroyTemporaryUploadRequest $request,
    ): JsonResponse|RedirectResponse {

        $temporaryUpload = $this->mediaService->resolveTemporaryUploadModel($request->route('temporaryUploadId'));
        // Delete the medium
        $temporaryUpload->delete();

        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id; // non-xhr needs media-manager-id, xhr relies on initiatorId

        // Reorder remaining uploads
        $this->reorderAllMedia($request);

        return MediaResponse::success(
            $request,
            $initiatorId,
            $mediaManagerId,
            __('media-library-extensions::messages.medium_removed')
        );
    }

    protected function reorderAllMedia($request): void
    {
        $collections = collect($request->input('collections', []))
            ->filter() // remove empty or null entries
            ->values() // flatten to a simple indexed list
            ->all();

        if (empty($collections)) {
            Log::warning('No valid collections provided for reorderAllMedia.');

            return;
        }

        // For testing purposes use session id from header, otherwise real session
        $sessionId = $request->header('X-Test-Session-Id') ?? session()->getId();
        $instanceId = $request->input('instance_id');

        $temporaryUploads = TemporaryUpload::query()
            ->when($instanceId, fn ($q) => $q->where('instance_id', $instanceId))
            ->where('session_id', $sessionId)
            ->whereIn('collection_name', $collections)
            ->get()
            ->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX));

        //        $temporaryUploads = TemporaryUpload::where('session_id', $sessionId)
        //            ->whereIn('collection_name', $collections)
        //            ->get()
        //            ->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX));

        $priority = 0;
        foreach ($temporaryUploads as $temporaryUpload) {
            $temporaryUpload->setCustomProperty('priority', $priority++);
            $temporaryUpload->save();
        }
    }
}
