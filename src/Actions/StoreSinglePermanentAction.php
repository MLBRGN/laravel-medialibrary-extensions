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

        Log::info('UPLOAD REQUEST:', $request->all());

        $baseId = (string) $request->input('base_id');
        $modelType = $request->model_type;
        $modelId = $request->model_id;

        try {
            $prepared = $this->uploadPreparerService
                ->prepareSingleUpload($request);

            $dataSource = $request->data_source;

            $model = $this->mediaService->resolveModelById(
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
                    $baseId,
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
                $baseId,
                $e->getMessage()
            );

        } catch (Exception $e) {

            Log::error($e);

            return MediaResponse::error(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.something_went_wrong')
            );
        }

        return MediaResponse::success(
            $request,
            $baseId,
            __('medialibrary-extensions::messages.upload_success')
        );
    }
}
