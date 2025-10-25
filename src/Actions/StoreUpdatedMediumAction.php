<?php

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

class StoreUpdatedMediumAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(UpdateMediumRequest $request): JsonResponse|RedirectResponse
    {
        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id; // non-xhr needs media-manager-id, xhr relies on initiatorId

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

        if (! $temporaryUploadMode) {

            abort_unless(class_exists($modelType), 400, 'Invalid model type');

            $model = $this->mediaService->resolveModel($modelType, $modelId);

            $existingMedium = Media::find($mediumId);
            $priority = null;

            if ($existingMedium) {
                // Preserve the custom property 'priority'
                $priority = $existingMedium->getCustomProperty('priority');
                $order = $existingMedium->order_column;
                $existingMedium->delete();
            } else {
                $order = null;
                Log::warning("Media with ID {$mediumId} not found when trying to replace it.");
            }

            try {
                $newMedium = $model->addMedia($file)
                    ->toMediaCollection($collection);

                // Restore priority and order
                if ($priority !== null) {
                    $newMedium->setCustomProperty('priority', $priority);
                }
                if ($order !== null) {
                    $newMedium->order_column = $order;
                }
                $newMedium->save();
            } catch (Exception $e) {
                return MediaResponse::error(
                    $request,
                    $initiatorId,
                    $mediaManagerId,
                    __('media-library-extensions::messages.something_went_wrong'),
                    [ 'mediumId' => $mediumId ]
                );

            }
//            Log::info('trying to find medium with id'.$mediumId);
        } else {
            $existingMedium = TemporaryUpload::find($mediumId);
            $priority = $existingMedium?->custom_properties['priority'] ?? null;

            $disk = config('media-library-extensions.temporary_upload_disk');
            $basePath = config('media-library-extensions.temporary_upload_path');
            // Save the new file
            $safeFilename = sanitizeFilename(pathinfo($existingMedium->name, PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();
            $filename = "{$safeFilename}.{$extension}";
            $directory = "{$basePath}";

            Storage::disk($disk)->putFileAs($directory, $file, $filename);

            $originalName = $file->getClientOriginalName();
            $mimetype = $file->getMimeType();
            $sessionId = $request->session()->getId();
            $userId = Auth::check() ? Auth::id() : null;

            $customProperties = $collections;
            if ($priority !== null) {
                $customProperties['priority'] = $priority;
            }

            $newMedium = new TemporaryUpload([
                'disk' => $disk,
                'path' => "{$directory}/{$filename}",
                'name' => $safeFilename,
                'file_name' => $originalName,
                'collection_name' => $collection,
                'mime_type' => $mimetype,
                'size' => $file->getSize(),
                'user_id' => $userId,
                'session_id' => $sessionId,
                'order_column' => $existingMedium?->order_column ?? 1,
                'custom_properties' => $customProperties,
            ]);
            $newMedium->save();

            if ($existingMedium) {
                $existingMedium->delete();
            } else {
                Log::warning("Media with ID {$mediumId} not found when trying to replace it.");
            }
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
