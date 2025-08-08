<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadYouTubeRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
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

        $initiatorId = $request->initiator_id;
        $collection = $request->collection_name;

        if ($request->boolean('temporary_upload')) {
            $tempUpload = $this->youTubeService->storeTemporaryThumbnailFromRequest($request);

            if (! $tempUpload) {
                return MediaResponse::error(
                    $request, $initiatorId,
                    __('media-library-extensions::messages.youtube_thumbnail_download_failed')
                );
            }

            return MediaResponse::success(
                $request, $initiatorId,
                __('media-library-extensions::messages.youtube_video_uploaded')
            );

        } else {

            $model = $this->mediaService->resolveModel($request->model_type, $request->model_id);
            $field = config('media-library-extensions.upload_field_name_youtube');

            if ($request->filled($field)) {
                $this->youTubeService->uploadThumbnailFromUrl(
                    model: $model,
                    youtubeUrl: $request->input($field),
                    collection: $collection
                );

                return MediaResponse::success($request, $initiatorId,
                    __('media-library-extensions::messages.youtube_video_uploaded'));
            }

            return MediaResponse::error($request, $initiatorId,
                __('media-library-extensions::messages.upload_no_youtube_url'));
        }
    }
}
