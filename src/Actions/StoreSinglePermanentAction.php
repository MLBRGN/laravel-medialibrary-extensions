<?php

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Exceptions\UploadException;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaModelResolver;
use Mlbrgn\MediaLibraryExtensions\Services\UploadPreparerService;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class StoreSinglePermanentAction
{
    use ChecksMediaLimits;

    public function __construct(
        protected MediaModelResolver $mediaModelResolver,
        protected UploadPreparerService $uploadPreparerService,
    ) {}

    public function execute(
        StoreSingleRequest $request
    ): RedirectResponse|JsonResponse {

        $baseId = (string) $request->input('base_id');
        $modelType = $request->model_type;
        $modelId = $request->model_id;

        try {
            $prepared = $this->uploadPreparerService
                ->prepareSingleUpload($request);

            $dataSource = $request->input('data_source', 'default');

            $model = $this->mediaModelResolver->resolveModelById(
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

            $customProperties = [
                'priority' => 0,
            ];

            if (str_contains($prepared->mimeType, 'image')) {
                $dimensions = getimagesize($prepared->file->getPathname());
                if ($dimensions) {
                    $customProperties['width'] = $dimensions[0];
                    $customProperties['height'] = $dimensions[1];
                }
            }

            $model->addMedia($prepared->file)
                ->withCustomProperties($customProperties)
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

            Log::error('StoreSinglePermanentAction - execute: '.$e->getMessage());

            return MediaResponse::error(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.could_not_save_media',
                    [
                        'file' => $prepared->originalName,
                        'message' => $e->getMessage(),
                    ]
                )
            );
        }

        return MediaResponse::success(
            $request,
            $baseId,
            __('medialibrary-extensions::messages.upload_success')
        );
    }
}
