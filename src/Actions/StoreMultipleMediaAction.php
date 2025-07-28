<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\YouTubeService;

class StoreMultipleMediaAction
{
    public function __construct(
        protected MediaService $mediaService,
        protected YouTubeService $youTubeService
    ) {}

    public function execute(MediaManagerUploadMultipleRequest $request): RedirectResponse|JsonResponse
    {
        if ($request->temporary_upload === 'yes') {
            abort(501, 'Temporary upload not yet implemented');
        }

        $model = $this->mediaService->resolveModel($request->model_type, $request->model_id);
        $initiatorId = $request->initiator_id;
        $field = config('media-library-extensions.upload_field_name_multiple');

        if ($request->hasFile($field)) {
            foreach ($request->file($field) as $file) {
                $collection = $this->mediaService->determineCollection($file);

                if (! $collection) {
                    return MediaResponse::error($request, $initiatorId, __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype'));
                }

                $model->addMedia($file)->toMediaCollection($collection);
            }

            return MediaResponse::success($request, $initiatorId, __('media-library-extensions::messages.upload_success'));
        }

        if ($request->filled('youtube_url')) {
            $this->youTubeService->uploadThumbnailFromUrl(
                model: $model,
                youtubeUrl: $request->input('youtube_url'),
                collection: 'workplace-youtube-videos',
                customId: $request->input('youtube_id')
            );
//            $videoId = extractYouTubeId($request->input('youtube_url'));
//
//            // TODO: Validate videoId?
//            $thumbnailUrl = "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
//
//            $model
//                ->addMediaFromUrl($thumbnailUrl)
//                ->usingFileName('youtube-thumbnail-'.$request->youtube_id.'.jpg')
//                ->withCustomProperties([
//                    'youtube-url' => $request->input('youtube_url'),
//                    'youtube-id' => $videoId,
//                ])
//                ->toMediaCollection('workplace-youtube-videos');

            return MediaResponse::success($request, $initiatorId, __('media-library-extensions::messages.youtube_video_uploaded'));
        }

        return MediaResponse::error($request, $initiatorId, __('media-library-extensions::messages.upload_no_files'));
    }
}
