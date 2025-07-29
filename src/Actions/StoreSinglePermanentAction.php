<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

class StoreSinglePermanentAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(MediaManagerUploadSingleRequest $request): RedirectResponse|JsonResponse
    {
        $model = $this->mediaService->resolveModel($request->model_type, $request->model_id);
        $initiatorId = $request->initiator_id;
        $field = config('media-library-extensions.upload_field_name_single');
        $file = $request->file($field);

        if (! $file) {
            return MediaResponse::error($request, $initiatorId, __('media-library-extensions::messages.upload_no_files'));
        }

        $collection = $this->mediaService->determineCollection($file);

        if (! $collection) {
            return MediaResponse::error($request, $initiatorId, __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype'));
        }

        $model->addMedia($file)->toMediaCollection($collection);

        return MediaResponse::success($request, $initiatorId, __('media-library-extensions::messages.upload_success'));
    }
}
