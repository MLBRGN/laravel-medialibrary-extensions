<?php

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\RestoreOriginalMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Throwable;

class RestoreOriginalMediaAction
{
    public function __construct(
        protected MediaService $mediaService,
    ) {}

    public function execute(
        RestoreOriginalMediumRequest $request,
        string|int $mediaId
    ): JsonResponse|RedirectResponse {
        $dataSource = $request->input('data_source');

        // Prefer ID from request if present, otherwise use from URL
        $id = $request->input('medium_id', $mediaId);

        $media = $this->mediaService->findMedium($id, $dataSource);

        Log::info('RestoreOriginalMediaAction - execute: $mediaId: ' . $mediaId);
        Log::info('RestoreOriginalMediaAction - execute: $dataSource: ' . $dataSource);

        $baseId = (string) ($request->input('base_id') ?? '');

        if (! $media) {
            Log::warning('RestoreOriginalMediaAction - execute: $media not found');
            return MediaResponse::error(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.medium_not_found')
            );
        }

        $originalsDisk = config('medialibrary-extensions.media_disks.originals');
        $originalPath = "{$media->id}/{$media->file_name}";

        if (! Storage::disk($originalsDisk)->exists($originalPath)) {
            Log::warning("Original not found at [$originalsDisk:$originalPath]");

            return MediaResponse::error(
                $request,
                $baseId,
                __('media-library-extensions::messages.no_original_saved')
            );
//            return back()->with('error', 'Original file not found.');
        }

        try {
            $targetDisk = $media->disk;
            if (! array_key_exists($targetDisk, config('filesystems.disks'))) {
                Log::warning("Disk [$targetDisk] not configured, using fallback [media]");
                $targetDisk = 'media';
            }

            $targetPath = $media->getPathRelativeToRoot(); // safer for Storage
            $content = Storage::disk($originalsDisk)->get($originalPath);

            // Write to medium path (on correct disk)
            Storage::disk($targetDisk)->put($targetPath, $content);

            //            Log::info("Restored original [$originalPath] → [$targetDisk:$targetPath]");

            // Mark conversions for regeneration
            foreach ($media->getMediaConversionNames() as $conversionName) {
                $media->markAsConversionNotGenerated($conversionName);
            }

            return MediaResponse::success(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.restored_original')
            );
        } catch (Throwable $e) {
            Log::error("Failed to restore original for media [{$media->id}]: {$e->getMessage()}");

            return MediaResponse::error(
                $request,
                $baseId,
                __('medialibrary-extensions::messages.could_not_restore_original')
            );
        }
    }
}
