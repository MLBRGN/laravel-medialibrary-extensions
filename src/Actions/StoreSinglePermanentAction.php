<?php

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Exceptions\UploadException;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\UploadPreparerService;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class StoreSinglePermanentAction
{
    use ChecksMediaLimits;

    public function __construct(
        protected MediaService $mediaService,
        protected UploadPreparerService $uploadPreparerService,
    ) {}

    public function execute(
        StoreSingleRequest $request
    ): RedirectResponse|JsonResponse {

        // Temporarily add this to see the payload
//        Log::info('UPLOAD PAYLOAD:', $request->all());

        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id;
        $modelType = $request->model_type;
        $modelId = $request->model_id;

        try {
            $prepared = $this->uploadPreparerService
                ->prepareSingleUpload($request);

            $dataSource = $request->data_source;

            $model = $this->mediaService->findMediaModel(
                $modelType,
                $modelId,
                $dataSource
            );

            if ($this->modelHasAnyMedia(
                $model,
                $prepared->collections,
                $dataSource
            )) {
                return MediaResponse::error(
                    $request,
                    $initiatorId,
                    $mediaManagerId,
                    __('medialibrary-extensions::messages.only_one_medium_allowed')
                );
            }

            $model->addMedia($prepared->file)
                ->withCustomProperties([
                    'priority' => 0,
                ])
                ->toMediaCollection(
                    $prepared->collectionName
                );

        } catch (UploadException $e) {

            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                $e->getMessage()
            );

        } catch (Exception $e) {

            Log::error($e);

            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('medialibrary-extensions::messages.something_went_wrong')
            );
        }

        return MediaResponse::success(
            $request,
            $initiatorId,
            $mediaManagerId,
            __('medialibrary-extensions::messages.upload_success')
        );
    }
}
