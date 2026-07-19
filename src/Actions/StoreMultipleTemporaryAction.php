<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\UploadPreparerService;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class StoreMultipleTemporaryAction
{
    // TODO use MediaService::countTemporaryUploadsInCollections() or countMediaInCollections()
    use ChecksMediaLimits;

    public function __construct(
        protected MediaService $mediaService,
        protected UploadPreparerService $uploadPreparerService,
    ) {}

    public function execute(StoreMultipleRequest $request): RedirectResponse|JsonResponse
    {
        $dataSource = $request->input('data_source', 'default');

        $disk = config('medialibrary-extensions.media_disks.temporary');
        $basePath = '';

        // Strict: only accept base_id; derive instance ID server-side
        $baseId = (string) $request->input('base_id');
        $instanceId = InstanceManager::getInstanceId($baseId);

        $files = $request->file('media');

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
        // IMPORTANT: For capacity checks we must consider ALL temporary uploads for the
        // same component instance, regardless of client_token. Tests seed existing
        // uploads with different client tokens and expect capping to apply globally
        // per instance+collection. Therefore, do NOT filter by client_token here.
        $temporaryUploadsInCollections = TemporaryUpload::query()
            ->forDataSource($dataSource)
            ->where('instance_id', $instanceId)
            ->whereIn('collection_name', array_values(array_filter($collections)))
            ->count();
        $nextPriority = $temporaryUploadsInCollections;

        if ($temporaryUploadsInCollections >= $maxItemsInCollection) {
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

        // Enforce remaining capacity: only process up to the remaining slots; mark overflow as failed.
        $remaining = max(0, $maxItemsInCollection - $temporaryUploadsInCollections);
        if ($remaining <= 0) {
            return MediaResponse::error(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.this_collection_can_contain_up_to_:items_items', [
                    'items' => $maxItemsInCollection,
                ])
            );
        }

        $overflowPrepared = [];
        if (count($preparedUploads) > $remaining) {
            $overflowPrepared = array_slice($preparedUploads, $remaining);
            $preparedUploads = array_slice($preparedUploads, 0, $remaining);
        }

        foreach ($preparedUploads as $prepared) {
            $originalName = $prepared->originalName;
            $extension = pathinfo($originalName, PATHINFO_EXTENSION) ?: $prepared->file->getClientOriginalExtension();
            $safeFilename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME), '-').'.'.$extension;

            $directory = "{$basePath}";
            $clientToken = $request->input('client_token')
                ?? $request->cookie('mle_client_token')
                ?? (string) Str::ulid();

            //            Storage::disk($disk)->putFileAs($directory, $prepared->file, $safeFilename);

            // Store file
            $path = Storage::disk($disk)->putFileAs(
                $directory,
                $prepared->file,
                $safeFilename
            );
            $temporaryUpload = $this->mediaService->make(TemporaryUpload::class, $dataSource);

            // Diagnostics: log upload context and resolved connection before persisting
            try {
                \Log::info('StoreMultipleTemporaryAction: preparing temporary upload', [
                    'data_source' => $dataSource,
                    'resolved_connection' => method_exists($temporaryUpload, 'getConnectionName') ? $temporaryUpload->getConnectionName() : null,
                    'instance_id' => $instanceId,
                    'client_token' => $clientToken,
                    'collection' => $prepared->collectionName,
                    'disk' => $disk,
                    'path' => $path ?? null,
                ]);
            } catch (\Throwable $e) {
                // ignore logging failures
            }

            $temporaryUpload->fill([
                'disk' => $disk,
                'path' => $path,
                'name' => $safeFilename,
                'file_name' => $safeFilename,
                'collection_name' => $prepared->collectionName,
                'mime_type' => $prepared->mimeType,
                'size' => $prepared->size,
                'user_id' => Auth::check() ? Auth::id() : null,
                'client_token' => $clientToken,
                'instance_id' => $instanceId,
                'order_column' => $nextPriority,
                'custom_properties' => [
                    'collections' => $prepared->collections,
                    'priority' => $nextPriority,
                ],
            ]);

            $temporaryUpload->save();

            // Diagnostics: confirm persisted record and connection
            Log::info('StoreMultipleTemporaryAction: temporary upload saved', [
                'temporary_upload_id' => $temporaryUpload->getKey(),
                'data_source' => $dataSource,
                'resolved_connection' => method_exists($temporaryUpload, 'getConnectionName') ? $temporaryUpload->getConnectionName() : null,
                'instance_id' => $instanceId,
                'client_token' => $clientToken,
                'collection' => $prepared->collectionName,
            ]);

            $nextPriority++;
            $successCount++;
        }

        // Append overflow info, if any
        if (! empty($overflowPrepared)) {
            $skipped = array_map(fn ($u) => $u->originalName, $overflowPrepared);
            $failedUploadFIleNames = array_merge($failedUploadFIleNames, $skipped);
            $errorMessages[] = __('medialibrary-extensions::messages.this_collection_can_contain_up_to_:items_items', [
                'items' => $maxItemsInCollection,
            ]);
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

        Log::info('{success_count} uploads successful', ['success_count' => $successCount]);
        Log::info('just before mediaresponse');

        return MediaResponse::success(
            $request,
            $baseId,
            $message,
            [
                'client_token' => $clientToken ?? null,
            ]
        );
    }
}
