<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreUpdatedMediaRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOriginalMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class StoreUpdatedMediaAction
{
    use InteractsWithOriginalMedia;

    public function __construct(protected MediaService $mediaService) {}

    public function execute(StoreUpdatedMediaRequest $request): JsonResponse|RedirectResponse
    {
        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id;
        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');
        $mediaId = $request->input('medium_id');
        $singleMediaId = $request->input('single_media_id');
        $collection = $request->input('collection');
        $temporaryUploadMode = $request->boolean('temporary_upload_mode');
        $file = $request->file('file');
        $collections = $request->array('collections');
        $dataSource = $request->input('data_source');

        $issingleMedia = $singleMediaId !== null && $singleMediaId !== 'null';
        $newMedia = null;
        if (empty($collections)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('medialibrary-extensions::messages.no_media_collections')
            );
        }

        try {
            // Handle Permanent Media
            if (! $temporaryUploadMode) {
                $existingMedia = $this->mediaService->findMedium($mediaId, $dataSource);
                if ($existingMedia) {
                    // Replace medium using old original
                    $newMedia = $this->replaceMedium($existingMedia, $file);
                } else {
                    Log::warning("Medium with ID {$mediaId} not found.");
                }
                // TODO when does this happen?
                //                else {
                //                    // Create new medium if not found
                //                    $newMedia = $model->addMedia($file)
                //                        ->toMediaCollection($collection);
                //
                //                    // Assign global_order for new media
                //                    $this->ensureGlobalOrder($newMedia);
                //                }
            }
            // Handle Temporary Uploads
            else {
                $existingMedia = $this->mediaService->findTemporaryUpload($mediaId, $dataSource);

                if ($existingMedia) {
                    $newMedia = $this->replaceTemporaryUpload($existingMedia, $file);
                }
                // TODO when does this happen?
                //                else {
                //                    Log::warning("TemporaryUpload with ID {$mediaId} not found; creating new.");
                //
                //                    $disk = config('medialibrary-extensions.media_disks.temporary');
                //                    $basePath = '';
                //
                //                    $safeFilename = sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                //                    $extension = $file->getClientOriginalExtension();
                //                    $filename = "{$safeFilename}.{$extension}";
                //                    $directory = "{$basePath}";
                //
                //                    $path = Storage::disk($disk)->putFileAs($directory, $file, $filename);
                //
                //                    $newMedia = TemporaryUpload::create([
                //                        'disk' => $disk,
                //                        'path' => $path,
                //                        'name' => $safeFilename,
                //                        'file_name' => $file->getClientOriginalName(),
                //                        'collection_name' => $collection,
                //                        'mime_type' => $file->getMimeType(),
                //                        'size' => $file->getSize(),
                //                        'user_id' => Auth::id(),
                //                        'client_token' => $clientToken,
                //                        'order_column' => 1,
                //                        'custom_properties' => $collections,
                //                        //                        'instance_id' => $instanceId,// TODO
                //                    ]);
                //                }
            }

        } catch (Exception $e) {
            Log::error("Failed to replace medium [{$mediaId}]: {$e->getMessage()}");

            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('medialibrary-extensions::messages.something_went_wrong'),
                [
                    'mediumId' => $mediaId, // TODO rename to mediaId
                    'exception' => $e->getMessage(),
                ]
            );
        }

        return MediaResponse::success(
            $request,
            $initiatorId,
            $mediaManagerId,
            __('medialibrary-extensions::messages.medium_replaced'),
            [
                'mediumId' => $mediaId, // TODO rename to mediaId
                'newMediumId' => $newMedia?->id,
                'singleMediaId' => $issingleMedia ? $newMedia?->id : null,
            ]
        );
    }
}
