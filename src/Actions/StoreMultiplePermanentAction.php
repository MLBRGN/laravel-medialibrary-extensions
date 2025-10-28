<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class StoreMultiplePermanentAction
{
    use ChecksMediaLimits;

    public function __construct(
        protected MediaService $mediaService,
    ) {}

    public function execute(StoreMultipleRequest $request): RedirectResponse|JsonResponse
    {
        $model = $this->mediaService->resolveModel($request->model_type, $request->model_id);

        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id; // non-xhr needs media-manager-id, xhr relies on initiatorId

        $field = config('media-library-extensions.upload_field_name_multiple');
        $files = $request->file($field);

        if (empty($files)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.upload_no_files')
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

        $maxItemsInCollection = config('media-library-extensions.max_items_in_shared_media_collections');
        $mediaInCollections = $this->countModelMediaInCollections($model, $collections);
        $nextPriority = $mediaInCollections;

        if ($mediaInCollections >= $maxItemsInCollection) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.this_collection_can_contain_up_to_:items_items', [
                    'items' => $maxItemsInCollection,
                ])
            );
        }

        $successCount = 0;
        $maxUploadSize = (int) config('media-library-extensions.max_upload_size');
        $failedUploadFIleNames = [];
        $errorMessages = [];

        // Check file sizes before proceeding
        foreach ($files as $key => $file) {
            if ($file->getSize() > $maxUploadSize) {
                $failedUploadFIleNames[] = $file->getClientOriginalName();
                $errorMessages[] = __(
                    'media-library-extensions::messages.file_too_large',
                    [
                        'file' => $file->getClientOriginalName(),
                        'max' => number_format($maxUploadSize / 1024 / 1024, 2) . ' MB',
                    ]
                );
                // Remove it from list so it’s not processed further
                unset($files[$key]);
            }
        }

        if (empty($files)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.no_valid_files_provided') . ' ' . implode(' ', $errorMessages)
            );
        }

        foreach ($files as $file) {
            $collectionType = $this->mediaService->determineCollectionType($file);
            $collectionName = $collections[$collectionType] ?? null;

            if (is_null($collectionType) || is_null($collectionName)) {
                $failedUploadFIleNames[] = $file->getClientOriginalName();
                $errorMessages[] = __(
                    'media-library-extensions::messages.invalid_or_missing_collection',
                    ['file' => $file->getClientOriginalName()]
                );
                continue;
            }

            try {
                $model->addMedia($file)
                    ->withCustomProperties([
                        'priority' => $nextPriority,
                    ])
                    ->toMediaCollection($collectionName);
                $nextPriority++;
                $successCount++;
            } catch (Exception $e) {
                Log::error($e);
                $failedUploadFIleNames[] = $file->getClientOriginalName();
                $errorMessages[] = __(
                    'media-library-extensions::messages.something_went_wrong',
                    ['file' => $file->getClientOriginalName()]
                );
                $errorMessages[] = $e->getMessage();
            }
        }

        if ($successCount === 0) {
            $message = __('media-library-extensions::messages.upload_failed');

            if (!empty($errorMessages)) {
                $message .= ' ' . implode(' ', $errorMessages);
            }

            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                $message
            );
        }

        $message = __('media-library-extensions::messages.upload_success');
        if (! empty($failedUploadFIleNames)) {
            $message .= ' '.__('media-library-extensions::messages.some_uploads_failed', [
                'files' => implode(', ', $failedUploadFIleNames),
            ]);
        }

        return MediaResponse::success(
            $request,
            $initiatorId,
            $mediaManagerId,
            $message
        );
    }
}
