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

class StoreYouTubeVideoPermanentAction
{
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
        $modelType = $request->model_type;
        $modelId = $request->model_id;
        $dataSource = $request->input('data_source');

        $initiatorId = $request->initiator_id;
        $mediaManagerDomId = $request->media_manager_id; // non-xhr needs media-manager-dom-id, xhr relies on initiatorId

        $collection = $request->youtube_collection;
        $multiple = $request->boolean('multiple');

        $collections = $request->array('collections');

        if (empty($collections)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerDomId,
                __('medialibrary-extensions::messages.no_media_collections')
            );
        }

        //        $model = $this->mediaService->resolveModel($request->model_type, $request->model_id);
        $model = $this->mediaService->findMediaModel($modelType, $modelId, $dataSource);
        $model->load(['media' => fn ($q) => $q->whereIn('collection_name', $collections)]);

        $maxItemsInCollection = config('medialibrary-extensions.max_items_in_shared_media_collections');
        if (! $multiple) {
            $maxItemsInCollection = 1;
        }
        $currentMediaCount = $this->countModelMediaInCollections($model, $collections);
        $nextPriority = $currentMediaCount;

        if ($currentMediaCount >= $maxItemsInCollection) {
            $message = $maxItemsInCollection === 1
                ? __('medialibrary-extensions::messages.only_one_medium_allowed')
                : __('medialibrary-extensions::messages.this_collection_can_contain_up_to_:items_items', [
                    'items' => $maxItemsInCollection,
                ]);

            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerDomId,
                $message
            );
        }

        if ($request->filled('youtube_url')) {
            $thumbnail = $this->youTubeService->uploadThumbnailFromUrl(
                model: $model,
                youtubeUrl: $request->input('youtube_url'),
                collection: $collection,
                dataSource: $dataSource
            );

            if (! $thumbnail) {
                return MediaResponse::error(
                    $request,
                    $initiatorId,
                    $mediaManagerDomId,
                    __('medialibrary-extensions::messages.youtube_thumbnail_download_failed')
                );
            }

            $thumbnail->setCustomProperty('priority', $nextPriority);
            $thumbnail->save();

            return MediaResponse::success(
                $request,
                $initiatorId,
                $mediaManagerDomId,
                __('medialibrary-extensions::messages.youtube_video_uploaded'));
        }

        return MediaResponse::error(
            $request,
            $initiatorId,
            $mediaManagerDomId,
            __('medialibrary-extensions::messages.upload_no_youtube_url'));
    }
}
