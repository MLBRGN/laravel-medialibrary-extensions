<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreUpdatedMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Models\Media;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

class StoreUpdatedMediumAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(StoreUpdatedMediumRequest $request): JsonResponse|RedirectResponse
    {
        $initiatorId = $request->initiator_id;

        Log::info('All request data', $request->all());

        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');
        $mediumId = $request->input('medium_id');
        $collection = $request->input('collection');
        $temporaryUpload = $request->boolean('temporary_upload');
        $file = $request->file('file');

        //        if (! $file) {
        //            return MediaResponse::error($request, $initiatorId, __('media-library-extensions::messages.upload_no_files'));
        //        }

        //            if (! $collection) {
        //                return MediaResponse::error($request, $initiatorId, __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype'));
        //            }

        if (! $temporaryUpload) {

            abort_unless(class_exists($modelType), 400, 'Invalid model type');

            $model = $this->mediaService->resolveModel($modelType, $modelId);
            $model->addMedia($file)->toMediaCollection($collection);

            // Find the existing media to replace
            $existingMedium = Media::findOrFail($mediumId);

            if ($existingMedium) {
                $existingMedium->delete();
            }

        } else {
            $existingMedium = TemporaryUpload::findOrFail($mediumId);

            $disk = config('media-library-extensions.temporary_upload_disk');
            $basePath = config('media-library-extensions.temporary_upload_path');
            // 📁 Save the new file
            $safeFilename = sanitizeFilename(pathinfo($existingMedium->name, PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();
            $filename = "{$safeFilename}.{$extension}";
            $directory = "{$basePath}";

            Storage::disk($disk)->putFileAs($directory, $file, $filename);

            $originalName = $file->getClientOriginalName();
            $mimetype = $file->getMimeType();

            $sessionId = $request->session()->getId();
            $userId = Auth::check() ? Auth::id() : null;

            $upload = new TemporaryUpload([
                'disk' => $disk,
                'path' => "{$directory}/{$filename}",
                'name' => $safeFilename,
                'file_name' => $originalName,
                'collection_name' => $collection,
                'mime_type' => $mimetype,
                'user_id' => $userId,
                'session_id' => $sessionId,
                'order_column' => 1,
                'custom_properties' => [
                    'image_collection' => $request->input('image_collection'),
                    'document_collection' => $request->input('document_collection'),
                    'youtube_collection' => $request->input('youtube_collection'),
                ],
            ]);
            $upload->save();

            $existingMedium->delete();
        }

        return MediaResponse::success($request, $initiatorId, __('media-library-extensions::messages.medium_replaced'));
    }
}
