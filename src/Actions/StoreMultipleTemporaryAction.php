<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class StoreMultipleTemporaryAction
{
    use ChecksMediaLimits;

    public function __construct(
        protected MediaService $mediaService,
    ) {}

    public function execute(StoreMultipleRequest $request): RedirectResponse|JsonResponse
    {
        $disk = config('media-library-extensions.temporary_upload_disk');
        $basePath = config('media-library-extensions.temporary_upload_path');

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
        $temporaryUploadsInCollections = $this->countTemporaryUploadsInCollections($collections);
        $nextPriority = $temporaryUploadsInCollections;

        if ($temporaryUploadsInCollections >= $maxItemsInCollection) {
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

            $originalName = $file->getClientOriginalName();
            $directory = "{$basePath}";
            $sessionId = $request->session()->getId();
            $safeFilename = sanitizeFilename(pathinfo($originalName, PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();
            $filename = "{$safeFilename}.{$extension}";

            // Store file
            Storage::disk($disk)->putFileAs($directory, $file, $filename);

            // Create DB record
            $upload = new TemporaryUpload([
                'disk' => $disk,
                'path' => "{$directory}/{$filename}",
                'name' => $safeFilename,
                'file_name' => $originalName,
                'collection_name' => $collectionName,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'user_id' => Auth::check() ? Auth::id() : null,
                'session_id' => $sessionId,
                'order_column' => $nextPriority,
                'custom_properties' => [
                    'collections' => json_encode($collections),
                    'priority' => $nextPriority,
                ],
            ]);

            $nextPriority++;

            $upload->save();
            $successCount++;
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
