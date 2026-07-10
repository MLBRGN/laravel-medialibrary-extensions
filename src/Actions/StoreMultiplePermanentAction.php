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
use Mlbrgn\MediaLibraryExtensions\Services\UploadPreparerService;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class StoreMultiplePermanentAction
{
    use ChecksMediaLimits;

    public function __construct(
        protected MediaService $mediaService,
        protected UploadPreparerService $uploadPreparerService,
    ) {}

    public function execute(StoreMultipleRequest $request): RedirectResponse|JsonResponse
    {
        $modelType = $request->model_type;
        $modelId = $request->model_id;

        $dataSource = $request->input('data_source', 'default');

        try {
            $model = $this->mediaService->resolveModelById($modelType, $modelId, $dataSource);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
        $baseId = (string) $request->input('base_id');

        $files = $request->file('media', []);

        if (empty($files)) {
            return MediaResponse::error(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.upload_no_files')
            );
        }

        $collections = $request->array('collections');

        if (empty($collections)) {
            return MediaResponse::error(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.no_media_collections')
            );
        }

        $maxItemsInCollection = config('medialibrary-extensions.max_items_in_shared_media_collections');
        $mediaInCollections = $this->countModelMediaInCollections($model, $collections, $dataSource);
        $nextPriority = $mediaInCollections;

        if ($mediaInCollections >= $maxItemsInCollection) {
            return MediaResponse::error(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.this_collection_can_contain_up_to_:items_items', [
                    'items' => $maxItemsInCollection,
                ])
            );
        }

        $successCount = 0;
        $failedUploadFIleNames = [];
        $errorMessages = [];

        // Delegate validation & mapping to service
        $result = $this->uploadPreparerService->prepareMultipleUploads($files, $collections);

        $preparedUploads = $result['prepared'];
        $failedUploadFIleNames = array_merge($failedUploadFIleNames, $result['failedFilenames']);
        $errorMessages = array_merge($errorMessages, $result['errors']);

        if (empty($preparedUploads)) {
            $message = __('medialibrary-extensions::messages.upload_failed');
            if (! empty($errorMessages)) {
                $message .= ' '.implode(' ', $errorMessages);
            }

            return MediaResponse::error(
                $request,
                $baseId,
                $message
            );
        }

        foreach ($preparedUploads as $prepared) {
            try {
                Log::info('Adding media', [
                    'collection' => $prepared->collectionName,
                    'default_disk' => config('media-library.disk_name'),
                ]);

                $media = $model->addMedia($prepared->file)
                    ->withCustomProperties([
                        'priority' => $nextPriority,
                    ])
                    ->toMediaCollection($prepared->collectionName);

                Log::info('StoreMultiplePermanentAction - execute: Stored media', [
                    'disk' => $media->disk,
                    'conversions_disk' => $media->conversions_disk,
                    'path' => $media->getPath(),
                    'url' => $media->getUrl(),
                    'preview_url' => $media->hasGeneratedConversion('preview')
                        ? $media->getUrl('preview')
                        : null,
                ]);
                $nextPriority++;
                $successCount++;
            } catch (Exception $e) {
                Log::error($e);
                $failedUploadFIleNames[] = $prepared->originalName;
                $errorMessages[] = __(
                    'medialibrary-extensions::messages.could_not_save_media',
                    [
                        'file' => $prepared->originalName,
                        'message' => $e->getMessage(),
                    ]
                );
                $errorMessages[] = $e->getMessage();
            }
        }

        if ($successCount === 0) {
            $message = __('medialibrary-extensions::messages.upload_failed');

            if (! empty($errorMessages)) {
                $message .= ' '.implode(' ', $errorMessages);
            }

            return MediaResponse::error(
                $request,
                $baseId,
                $message
            );
        }

        Log::withContext([
            'base_id' => $baseId,
        ]);

        Log::info('{success_count} uploads successful', [
            'success_count' => $successCount,
        ]);

        $message = __('medialibrary-extensions::messages.upload_success');
        if (! empty($failedUploadFIleNames)) {
            $message .= ' '.__('medialibrary-extensions::messages.some_uploads_failed', [
                'files' => implode(', ', $failedUploadFIleNames),
            ]);
        }

        return MediaResponse::success(
            $request,
            $baseId,
            $message
        );
    }
}
