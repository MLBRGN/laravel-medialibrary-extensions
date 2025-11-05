<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\UpdateMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Models\Media;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
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
        $mediumId = $request->input('medium_id');
        $singleMediumId = $request->input('single_medium_id');
        $collection = $request->input('collection');
        $temporaryUploadMode = $request->boolean('temporary_upload_mode');
        $file = $request->file('file');
        $collections = $request->array('collections');

        $isSingleMedium = $singleMediumId !== null && $singleMediumId !== 'null';
        $newMedium = null;

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
                abort_unless(class_exists($modelType), 400, 'Invalid model type');

                $model = $this->mediaService->resolveModel($modelType, $modelId);
                //                $existingMedium = Media::find($mediumId);
                $existingMedium = Media::findOrFail($mediumId);

                if ($existingMedium) {
                    // Replace medium using old original
                    $newMedium = $this->replaceMedium($existingMedium, $file);
                } else {
                    // Create new medium if not found
                    $newMedium = $model->addMedia($file)
                        ->toMediaCollection($collection);

                    // Assign global_order for new media
                    $this->ensureGlobalOrder($newMedium);
                }
            }
            // Handle Temporary Uploads
            else {
                $existingMedium = TemporaryUpload::find($mediumId);

                if ($existingMedium) {
                    $newMedium = $this->replaceTemporaryUpload($existingMedium, $file);
                } else {
                    Log::warning("TemporaryUpload with ID {$mediumId} not found; creating new.");

                    $disk = config('media-library-extensions.media_disks.temporary');//config('media-library-extensions.temporary_upload_disk');
                    $basePath = '';//config('media-library-extensions.temporary_upload_path');

                    $safeFilename = sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    $extension = $file->getClientOriginalExtension();
                    $filename = "{$safeFilename}.{$extension}";
                    $directory = "{$basePath}";

                    $path = Storage::disk($disk)->putFileAs($directory, $file, $filename);

                    $newMedium = TemporaryUpload::create([
                        'disk' => $disk,
                        'path' => $path,
                        'name' => $safeFilename,
                        'file_name' => $file->getClientOriginalName(),
                        'collection_name' => $collection,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'user_id' => Auth::id(),
                        'session_id' => $request->session()->getId(),
                        'order_column' => 1,
                        'custom_properties' => $collections,
                    ]);
                }
            }

        } catch (Exception $e) {
            Log::error("Failed to replace medium [{$mediumId}]: {$e->getMessage()}");

            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.something_went_wrong'),
                [
                    'mediumId' => $mediumId,
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
                'mediumId' => $mediumId,
                'newMediumId' => $newMedium?->id,
                'singleMediumId' => $isSingleMedium ? $newMedium?->id : null,
            ]
        );
    }
}
