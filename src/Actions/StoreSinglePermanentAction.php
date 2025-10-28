<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class StoreSinglePermanentAction
{
    use ChecksMediaLimits;

    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(StoreSingleRequest $request): RedirectResponse|JsonResponse
    {
        $model = $this->mediaService->resolveModel($request->model_type, $request->model_id);
        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id; // non-xhr needs media-manager-id, xhr relies on initiatorId

        $field = config('media-library-extensions.upload_field_name_single');
        $file = $request->file($field);

        if (! $file) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.upload_no_files'));
        }

        $maxUploadSize = (int) config('media-library-extensions.max_upload_size');
        if ($file->getSize() > $maxUploadSize) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __(
                    'media-library-extensions::messages.file_too_large',
                    [
                        'file' => $file->getClientOriginalName(),
                        'max' => number_format($maxUploadSize / 1024 / 1024, 2) . ' MB',
                    ]
                )
            );
        }

        $collections = $request->array('collections');

        if (empty($collections)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.no_media_collections')
            );
        }

        if ($this->modelHasAnyMedia($model, $collections)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.only_one_medium_allowed')
            );
        }

        $collectionType = $this->mediaService->determineCollectionType($file);
        $collectionName = $collections[$collectionType] ?? null;

        if (is_null($collectionType)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype'));
        }

        if (is_null($collectionName)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.upload_failed_due_to_invalid_collection'));
        }

        try {
            $model->addMedia($file)
                ->withCustomProperties(['priority' => 0])
                ->toMediaCollection($collectionName);
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
