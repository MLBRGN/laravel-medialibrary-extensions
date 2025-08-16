<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetTemporaryUploadAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

class SetTemporaryUploadAsFirstAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(SetTemporaryUploadAsFirstRequest $request): JsonResponse|RedirectResponse
    {
        $initiatorId = $request->initiator_id;
        $mediumId = (int) $request->medium_id;

        $collections = collect([
            $request->input('image_collection'),
            $request->input('document_collection'),
            $request->input('youtube_collection'),
            $request->input('video_collection'),
            $request->input('audio_collection'),
        ])->filter()->all();

        // Get temporary uploads for this session limited to the given collections
        $mediaItems = TemporaryUpload::where('session_id', $request->session()->getId())
            ->when(!empty($collections), fn ($query) => $query->whereIn('collection_name', $collections))
            ->get();

        if ($mediaItems->isEmpty()) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                __('media-library-extensions::messages.no_media'),
            );
        }

        // Find the target upload
        $targetMedia = $mediaItems->firstWhere('id', $mediumId);
        if (!$targetMedia) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                __('media-library-extensions::messages.medium_not_found'),
            );
        }
        // Sort by current priority
        $sorted = $mediaItems->sortBy(fn($m) => $m->getCustomProperty('priority', PHP_INT_MAX));

        // Move target to front
        $reordered = $sorted->reject(fn($m) => $m->id === $mediumId)->prepend($targetMedia);

        // Reassign priorities
        $priority = 0;
        foreach ($reordered as $media) {
            $media->setCustomProperty('priority', $priority++);
            $media->save();
        }

        return MediaResponse::success(
            $request,
            $initiatorId,
            __('media-library-extensions::messages.medium_set_as_main')
        );
    }
}
