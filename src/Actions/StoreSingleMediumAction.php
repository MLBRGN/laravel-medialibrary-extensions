<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

class StoreSingleMediumAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(MediaManagerUploadSingleRequest $request): RedirectResponse|JsonResponse
    {
        if ($request->temporary_upload === 'yes') {
            abort(501, 'Temporary upload not yet implemented');
        }

        $model = $this->mediaService->resolveModel($request->model_type, $request->model_id);
        $collection = $this->mediaService->determineCollection($request->file(config('media-library-extensions.upload_field_name_single')));

        if (! $collection) {
            return MediaResponse::error($request, $request->initiator_id, __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype'));
        }

        $model->addMedia($request->file(config('media-library-extensions.upload_field_name_single')))
            ->toMediaCollection($collection);

        return MediaResponse::success($request, $request->initiator_id, __('media-library-extensions::messages.upload_success'));
    }
}
