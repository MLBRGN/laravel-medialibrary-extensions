<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\YouTubeService;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class StoreYouTubeVideoTemporaryAction
{
    // TODO use MediaService::countTemporaryUploadsInCollections() or countMediaInCollections()
    use ChecksMediaLimits;

    public function __construct(
        protected MediaService $mediaService,
        protected YouTubeService $youTubeService
    ) {}

    public function execute(StoreYouTubeVideoRequest $request): RedirectResponse|JsonResponse
    {
        if (! config('medialibrary-extensions.youtube_support_enabled')) {
            abort(403);
        }
        $dataSource = $request->input('data_source');

        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id; // non-xhr needs media-manager-dom-id, xhr relies on initiatorId
        $instanceId = $request->input('instance_id');

        $collection = $request->youtube_collection;
        $multiple = $request->boolean('multiple');

        $maxItemsInCollection = config('medialibrary-extensions.max_items_in_shared_media_collections');
        if (! $multiple) {
            $maxItemsInCollection = 1;
        }

        $collections = $request->array('collections');

        if (empty($collections)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('medialibrary-extensions::messages.no_media_collections')
            );
        }

        $temporaryUploadsInCollections = $this->countTemporaryUploadsInCollections($collections, $instanceId, null, $dataSource);
        $nextPriority = $temporaryUploadsInCollections;
        if ($temporaryUploadsInCollections >= $maxItemsInCollection) {
            $message = $maxItemsInCollection === 1
                ? __('medialibrary-extensions::messages.only_one_medium_allowed')
                : __('medialibrary-extensions::messages.this_collection_can_contain_up_to_:items_items', [
                    'items' => $maxItemsInCollection,
                ]);

            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                $message
            );
        }

        if ($request->filled('youtube_url')) {
            $tempUpload = $this->youTubeService->storeTemporaryThumbnailFromRequest($request);

            if (! $tempUpload) {
                return MediaResponse::error(
                    $request,
                    $initiatorId,
                    $mediaManagerId,
                    __('medialibrary-extensions::messages.youtube_thumbnail_download_failed')
                );
            }

            $tempUpload->instance_id = $instanceId;
            $tempUpload->setCustomProperty('priority', $nextPriority);
            $tempUpload->save();

            return MediaResponse::success(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('medialibrary-extensions::messages.youtube_video_uploaded')
            );
        }

        return MediaResponse::error(
            $request,
            $initiatorId,
            $mediaManagerId,
            __('medialibrary-extensions::messages.upload_no_youtube_url')
        );
    }
}
