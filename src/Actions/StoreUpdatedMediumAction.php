<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\UpdateMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOriginalMedia;

class StoreUpdatedMediumAction
{
    use InteractsWithOriginalMedia;

    public function __construct(protected MediaService $mediaService) {}

    public function execute(UpdateMediumRequest $request): JsonResponse|RedirectResponse
    {
        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id;
        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');
        $mediaId = $request->input('medium_id');
        $singleMediumId = $request->input('single_medium_id');
        $collection = $request->input('collection');
        $temporaryUploadMode = $request->boolean('temporary_upload_mode');
        $file = $request->file('file');
        $collections = $request->array('collections');

        $isSingleMedium = $singleMediumId !== null && $singleMediumId !== 'null';
        $newMedia = null;

        if (empty($collections)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.no_media_collections')
            );
        }

        try {
            // Handle Permanent Media
            if (! $temporaryUploadMode) {

                $model = $this->mediaService->resolveModel($modelType, $modelId);
                $existingMedia = $this->mediaService->resolveMediaModel($mediaId);
                if ($existingMedia) {
                    // Replace medium using old original
                    $newMedia = $this->replaceMedium($existingMedia, $file);
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
                $existingMedia = $this->mediaService->resolveTemporaryUploadModel($mediaId);

                if ($existingMedia) {
                    $newMedia = $this->replaceTemporaryUpload($existingMedia, $file);
                }
                // TODO when does this happen?
//                else {
//                    Log::warning("TemporaryUpload with ID {$mediaId} not found; creating new.");
//
//                    $disk = config('media-library-extensions.media_disks.temporary');
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
//                        'session_id' => $request->session()->getId(),
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
                __('media-library-extensions::messages.something_went_wrong'),
                [
                    'mediumId' => $mediaId,// TODO rename to mediaId
                    'exception' => $e->getMessage(),
                ]
            );
        }

        return MediaResponse::success(
            $request,
            $initiatorId,
            $mediaManagerId,
            __('media-library-extensions::messages.medium_replaced'),
            [
                'mediumId' => $mediaId,// TODO rename to mediaId
                'newMediumId' => $newMedia?->id,
                'singleMediumId' => $isSingleMedium ? $newMedia?->id : null,
            ]
        );
    }
}
