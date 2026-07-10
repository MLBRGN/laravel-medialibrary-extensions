<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\YouTubeService;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;

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
        $dataSource = $request->input('data_source', 'default');

        // Strict: only accept base_id; derive instance ID server-side
        $baseId = (string) $request->input('base_id');
        $instanceId = InstanceManager::getInstanceId($baseId);

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
                $baseId,
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
                $baseId,
                $message
            );
        }

        if ($request->filled('youtube_url')) {
            $tempUpload = $this->youTubeService->storeTemporaryThumbnailFromRequest($request);

            if (! $tempUpload) {
                return MediaResponse::error(
                    $request,
                    $baseId,
                    __('medialibrary-extensions::messages.youtube_thumbnail_download_failed')
                );
            }

            if ($dataSource) {
                $connection = app(DataSourceResolver::class)->resolveConnection($dataSource);
                $tempUpload->setConnection($connection);
            }

            $tempUpload->instance_id = $instanceId;
            $tempUpload->setCustomProperty('priority', $nextPriority);
            $tempUpload->save();

            return MediaResponse::success(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.youtube_video_uploaded'),
                [
                    // ensure client token persistence for subsequent preview fetches
                    'client_token' => $tempUpload->client_token ?? null,
                ]
            );
        }

        return MediaResponse::error(
            $request,
            $baseId,
            __('medialibrary-extensions::messages.upload_no_youtube_url')
        );
    }
}
