<?php


namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\RestoreOriginalMediumRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class RestoreOriginalMediumAction
{
    public function execute(
        RestoreOriginalMediumRequest $request,
        Media $media
    ): JsonResponse|RedirectResponse {
        $initiatorId = $request->initiator_id ?? '';
        $mediaManagerId = $request->media_manager_id ?? '';

        $originalsDisk = config('media-library-extensions.media_disks.originals');
        $originalPath = "{$media->id}/{$media->file_name}";

        if (!Storage::disk($originalsDisk)->exists($originalPath)) {
            Log::warning("Original not found at [$originalsDisk:$originalPath]");
            return back()->with('error', 'Original file not found.');
        }

        try {
            $targetDisk = $media->disk;
            if (!array_key_exists($targetDisk, config('filesystems.disks'))) {
                Log::warning("Disk [$targetDisk] not configured, using fallback [media]");
                $targetDisk = 'media';
            }

            $targetPath = $media->getPathRelativeToRoot(); // safer for Storage
            $content = Storage::disk($originalsDisk)->get($originalPath);

            // Write to medium path (on correct disk)
            Storage::disk($targetDisk)->put($targetPath, $content);

            Log::info("Restored original [$originalPath] → [$targetDisk:$targetPath]");

            // Mark conversions for regeneration
            foreach ($media->getMediaConversionNames() as $conversionName) {
                $media->markAsConversionNotGenerated($conversionName);
            }

            return MediaResponse::success(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.restored_original')
            );
        } catch (Throwable $e) {
            Log::error("Failed to restore original for media [{$media->id}]: {$e->getMessage()}");

            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.could_not_restore_original')
            );
        }
    }
}

//
///** @noinspection PhpMultipleClassDeclarationsInspection */
//
//namespace Mlbrgn\MediaLibraryExtensions\Actions;
//
//use Illuminate\Http\JsonResponse;
//use Illuminate\Http\RedirectResponse;
//use Illuminate\Support\Facades\Log;
//use Illuminate\Support\Facades\Storage;
//use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
//use Mlbrgn\MediaLibraryExtensions\Http\Requests\RestoreOriginalMediumRequest;
//use Spatie\MediaLibrary\MediaCollections\Models\Media;
//
//class RestoreOriginalMediumAction
//{
//    public function execute(
//        RestoreOriginalMediumRequest $request,
//        Media $media
//    ): JsonResponse|RedirectResponse {
//        $initiatorId = $request->initiator_id ?? '';
//        $mediaManagerId = $request->media_manager_id ?? ''; // non-xhr needs media-manager-id, xhr relies on initiatorId
//
//        $model = $media->model;
//
//        $mediaDisk = $media->disk;
//        $mediaPath = $media->getPath();
//
//        $originalDisk = $mediaDisk;
//        $originalPath = "{$media->id}/{$media->file_name}";
//
//        Log::info('RestoreOriginalMediumAction originalPath: '.$originalPath . ' disk: '.$originalDisk);
//        Log::info('RestoreOriginalMediumAction medium: '.$media);
//        if (! Storage::disk(config('media-library-extensions.media_disks.originals'))->exists($originalPath)) {
//            return back()->with('error', 'Original file not found.');
//        }
//
//        try {
//            $targetDisk = $media->disk;
//
//            if (! array_key_exists($targetDisk, config('filesystems.disks'))) {
//                Log::warning("Disk [$targetDisk] not configured, using fallback [media]");
//                $targetDisk = 'media';
//            } else {
//                Log::info("Disk [$targetDisk] is configured, using [media_demo]");
//
//            }
//
//            // Overwrite current media file with original
//            $content = Storage::disk(config('media-library-extensions.media_disks.originals'))->get($originalPath);
//            $path = $media->getPath();
//            Log::info('RestoreOriginalMediumAction path: '.$path);
//
//            file_put_contents($path, $content);
//
//            Log::info("RestoreOriginalMediumAction restored original from [originals] to disk [$targetDisk] at path [$path]");
//
//            // request regenerating conversions
//            $mediaConversionNames = $media->getMediaConversionNames();
//            foreach ($mediaConversionNames as $mediaConversionName) {
//                $media->markAsConversionNotGenerated($mediaConversionName);
//            }
//
//            Log::info('RestoreOriginalMediumAction media->path(): '.$media->getPath());
//
//            return MediaResponse::success(
//                $request,
//                $initiatorId,
//                $mediaManagerId,
//                __('media-library-extensions::messages.restored_original')
//            );
//        } catch (\Throwable $e) {
//
//            Log::error("Failed to restore original for media [$media->id]: {$e->getMessage()}");
//
//            return MediaResponse::error(
//                $request,
//                $initiatorId,
//                $mediaManagerId,
//                __('media-library-extensions::messages.could_not_restore_original')
//            );
//        }
//    }
//}
