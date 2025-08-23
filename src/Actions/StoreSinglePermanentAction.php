<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class StoreSinglePermanentAction
{

    use ChecksMediaLimits;

    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(MediaManagerUploadSingleRequest $request): RedirectResponse|JsonResponse
    {
        $model = $this->mediaService->resolveModel($request->model_type, $request->model_id);
        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id;// non-xhr needs media-manager-id, xhr relies on initiatorId

        $field = config('media-library-extensions.upload_field_name_single');
        $file = $request->file($field);

        if (! $file) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.upload_no_files'));
        }

        $collections = collect([
            $request->input('image_collection'),
            $request->input('document_collection'),
            $request->input('youtube_collection'),
            $request->input('video_collection'),
            $request->input('audio_collection'),
        ])->filter()->all();// remove falsy values

        if ($this->modelHasAnyMedia($model, $collections)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.only_one_medium_allowed')
            );
        }

        $collection = $this->mediaService->determineCollection($file);

        if (! $collection) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype'));
        }

        try {
            $model->addMedia($file)
                ->withCustomProperties(['priority' => 0])
                ->toMediaCollection($collection);
        } catch (Exception $e) {
            Log::error($e);
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.something_went_wrong')
            );
        }

        return MediaResponse::success(
            $request,
            $initiatorId,
            $mediaManagerId,
            __('media-library-extensions::messages.upload_success'));
    }
}
