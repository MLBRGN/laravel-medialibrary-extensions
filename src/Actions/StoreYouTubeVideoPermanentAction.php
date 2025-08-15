<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
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
        if (! config('media-library-extensions.youtube_support_enabled')) {
            abort(403);
        }

        $initiatorId = $request->initiator_id;
        $collection = $request->youtube_collection;
        $multiple = $request->boolean('multiple');

        $model = $this->mediaService->resolveModel($request->model_type, $request->model_id);
        $field = config('media-library-extensions.upload_field_name_youtube');

        if (!$multiple) {

            $collections = collect([
                $request->input('image_collection'),
                $request->input('document_collection'),
                $request->input('youtube_collection'),
                $request->input('video_collection'),
                $request->input('audio_collection'),
            ])->filter()->all();

            if ($this->modelHasAnyMedia($model, $collections)) {
                return MediaResponse::error(
                    $request,
                    $request->initiator_id,
                    __('media-library-extensions::messages.only_one_medium_allowed')
                );
            }
        }

        if ($request->filled($field)) {
            $thumbnail = $this->youTubeService->uploadThumbnailFromUrl(
                model: $model,
                youtubeUrl: $request->input($field),
                collection: $collection
            );

            if (! $thumbnail) {
                return MediaResponse::error(
                    $request,
                    $initiatorId,
                    __('media-library-extensions::messages.youtube_thumbnail_download_failed')
                );
            }

            return MediaResponse::success($request, $initiatorId,
                __('media-library-extensions::messages.youtube_video_uploaded'));
        }

        return MediaResponse::error($request, $initiatorId,
            __('media-library-extensions::messages.upload_no_youtube_url'));
    }
}
