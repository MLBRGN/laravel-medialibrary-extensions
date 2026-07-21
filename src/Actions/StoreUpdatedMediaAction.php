<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreUpdatedMediaRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaReplacement;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

class StoreUpdatedMediaAction
{

    public function __construct(
        protected MediaService $mediaService,
        protected MediaReplacement $mediaReplacement
    ) {}

    public function execute(StoreUpdatedMediaRequest $request): JsonResponse|RedirectResponse
    {
        $baseId = (string) $request->input('base_id');
        $mediaId = $request->input('medium_id');
        $singleMediaId = $request->input('single_media_id');
        $temporaryUploadMode = $request->boolean('temporary_upload_mode');
        $file = $request->file('file');
        $collections = $request->array('collections');
        $dataSource = $request->input('data_source', 'default');
        $isSingleMedia = $singleMediaId !== null && $singleMediaId !== 'null';
        $newMedia = null;

        $oldMediaId = $mediaId;
        if (empty($collections)) {
            return MediaResponse::error(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.no_media_collections')
            );
        }

        try {
            // Handle Permanent Media
            if (! $temporaryUploadMode) {
                $existingMedia = $this->mediaService->findMedium($mediaId, $dataSource);
                if ($existingMedia) {
                    // Ensure the medium actually belongs to the authorized model resolved by the request
                    $authorizedModel = $this->mediaService->resolveModelById(
                        $request->input('model_type'),
                        $request->input('model_id'),
                        $dataSource
                    );
                    if (! $authorizedModel || ! $existingMedia->model || ! $existingMedia->model->is($authorizedModel)) {
                        return MediaResponse::forbidden(
                            $request,
                            $baseId,
                            __('medialibrary-extensions::messages.not_authorized')
                        );
                    }

                    // store the updated medium and remove the old one
                    $newMedia = $this->mediaReplacement->replaceMedium($existingMedia, $file);
                } else {
                    Log::warning("Medium with ID {$mediaId} not found.");
                    return MediaResponse::error(
                        $request,
                        $baseId,
                        __('medialibrary-extensions::messages.medium_not_found')
                    );
                }
            }

            // Handle Temporary Uploads
            else {
                $existingMedia = $this->mediaService->findTemporaryUpload($mediaId, $dataSource);

                if (! $existingMedia) {
                    throw new Exception("Temporary upload with ID {$mediaId} not found.");
                }

                $newMedia = $this->mediaReplacement->replaceTemporaryUpload($existingMedia, $file);
            }

        } catch (Exception $e) {
            Log::error("Failed to replace medium [{$mediaId}]: {$e->getMessage()}");

            return MediaResponse::error(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.could_not_save_updated_medium'),
                [
                    'mediumId' => $mediaId,
                    'exception' => $e->getMessage(),
                ]
            );
        }

        Log::info("Medium with ID {$oldMediaId} replaced with ID {$newMedia?->id}");

        return MediaResponse::success(
            $request,
            $baseId,
            __('medialibrary-extensions::messages.medium_replaced'),
            [
                'oldMediumId' => $oldMediaId,
                'newMediumId' => $newMedia?->id,
                'singleMediaId' => $isSingleMedia ? $newMedia?->id : null,
            ]
        );
    }
}
