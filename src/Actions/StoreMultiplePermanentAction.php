<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;
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
        $modelType = $request->model_type;
        $modelId = $request->model_id;

        $dataSource = $request->input('data_source');

        $model = $this->mediaService->findMediaModel($modelType, $modelId, $dataSource);

        //        Log::info('After findMediaModel', [
        //            'connection' => $model->getConnectionName(),
        //            'database' => $model->getConnection()->getDatabaseName(),
        //        ]);

        $baseId = (string) $request->input('base_id');
        $instanceId = $request->input('instance_id');

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
        $maxUploadSize = (int) config('medialibrary-extensions.max_upload_size');
        $failedUploadFIleNames = [];
        $errorMessages = [];

        // Check file sizes before proceeding
        foreach ($files as $key => $file) {
            if ($file->getSize() > $maxUploadSize) {
                $failedUploadFIleNames[] = $file->getClientOriginalName();
                $errorMessages[] = __(
                    'medialibrary-extensions::messages.file_too_large',
                    [
                        'file' => $file->getClientOriginalName(),
                        'max' => number_format($maxUploadSize / 1024 / 1024, 2).' MB',
                    ]
                );
                // Remove it from list so it’s not processed further
                unset($files[$key]);
            }
        }

        if (empty($files)) {
            return MediaResponse::error(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.no_valid_files_provided').' '.implode(' ', $errorMessages)
            );
        }

        foreach ($files as $file) {
            $collectionType = $this->mediaService->determineCollectionType($file);
            $collectionName = $collections[$collectionType] ?? null;

            if (is_null($collectionType) || is_null($collectionName)) {
                $failedUploadFIleNames[] = $file->getClientOriginalName();
                $errorMessages[] = __(
                    'medialibrary-extensions::messages.invalid_or_missing_collection',
                    ['file' => $file->getClientOriginalName()]
                );

                continue;
            }

            //            Log::info('StoreMultiplePermanentAction - store in db: '.json_encode([
            //                'connection' => $model->getConnection()->getName(),
            //                'database' => $model->getConnection()->getDatabaseName(),
            //            ]));

            //            Log::info('Before addMedia', [
            //                'datasource' => $dataSource,
            //                'resolved' => app(DataSourceResolver::class)->resolveConnection($dataSource),
            //                'model_connection_name' => $model->getConnectionName(),
            //                'model_database' => $model->getConnection()->getDatabaseName(),
            //                'media_model' => config('media-library.media_model'),
            //            ]);

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
                    'medialibrary-extensions::messages.something_went_wrong',
                    ['file' => $file->getClientOriginalName()]
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
