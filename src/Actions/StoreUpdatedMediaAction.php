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
        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');
        $mediaId = $request->input('medium_id');
        $singleMediaId = $request->input('single_media_id');
        $collection = $request->input('collection');
        $temporaryUploadMode = $request->boolean('temporary_upload_mode');
        $file = $request->file('file');
        $collections = $request->array('collections');
        $dataSource = $request->input('data_source');
        $isSingleMedia = $singleMediaId !== null && $singleMediaId !== 'null';
        $newMedia = null;

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

                if ($existingMedia) {
                    $newMedia = $this->mediaReplacement->replaceTemporaryUpload($existingMedia, $file);
                }
            }

        } catch (Exception $e) {
            Log::error("Failed to replace medium [{$mediaId}]: {$e->getMessage()}");

            return MediaResponse::error(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.something_went_wrong'),
                [
                    'mediumId' => $mediaId,
                    'exception' => $e->getMessage(),
                ]
            );
        }

        return MediaResponse::success(
            $request,
            $baseId,
            __('medialibrary-extensions::messages.medium_replaced'),
            [
                'oldMediumId' => $mediaId,
                'newMediumId' => $newMedia?->id,
                'singleMediaId' => $isSingleMedia ? $newMedia?->id : null,
            ]
        );
    }
}
