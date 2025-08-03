<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadYouTubeRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\YouTubeService;

class StoreYouTubeMediaAction
{
    public function __construct(
        protected MediaService $mediaService,
        protected YouTubeService $youTubeService
    ) {}

    public function execute(MediaManagerUploadYouTubeRequest $request): RedirectResponse|JsonResponse
    {
        if (! config('media-library-extensions.youtube_support_enabled')) {
            abort(403);
        }

        if ($request->temporary_upload === 'true') {
            abort(501, 'Temporary upload not yet implemented');
        }

        $model = $this->mediaService->resolveModel($request->model_type, $request->model_id);
        $initiatorId = $request->initiator_id;
        $collection = $request->collection_name;
        $field = config('media-library-extensions.upload_field_name_youtube');

        if ($request->filled($field)) {
            $this->youTubeService->uploadThumbnailFromUrl(
                model: $model,
                youtubeUrl: $request->input($field),
                collection: $collection
            );

            return MediaResponse::success($request, $initiatorId, __('media-library-extensions::messages.youtube_video_uploaded'));
        }

        return MediaResponse::error($request, $initiatorId, __('media-library-extensions::messages.upload_no_youtube_url'));
    }
}
