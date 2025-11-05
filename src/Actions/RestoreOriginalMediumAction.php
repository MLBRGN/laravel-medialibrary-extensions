<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\RestoreOriginalMediumRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RestoreOriginalMediumAction
{
    public function execute(
        RestoreOriginalMediumRequest $request,
        Media $media
    ): JsonResponse|RedirectResponse {
        $initiatorId = $request->initiator_id ?? '';
        $mediaManagerId = $request->media_manager_id ?? ''; // non-xhr needs media-manager-id, xhr relies on initiatorId

        $model = $media->model;

        $originalPath = "{$media->id}/{$media->file_name}";

        Log::info('RestoreOriginalMediumAction originalPath: '.$originalPath);
        if (! Storage::disk(config('media-library-extensions.media_disks.originals'))->exists($originalPath)) {
            return back()->with('error', 'Original file not found.');
        }

        try {
            // Overwrite current media file with original
            $content = Storage::disk(config('media-library-extensions.media_disks.originals'))->get($originalPath);
            file_put_contents($media->getPath(), $content);

            Log::info('RestoreOriginalMediumAction stored original in media path: '.$originalPath);

            // request regenerating conversions
            $mediaConversionNames = $media->getMediaConversionNames();
            foreach ($mediaConversionNames as $mediaConversionName) {
                $media->markAsConversionNotGenerated($mediaConversionName);
            }

            Log::info('RestoreOriginalMediumAction media->path(): '.$media->getPath());

            return MediaResponse::success(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.restored_original')
            );
        } catch (\Throwable $e) {

            Log::error("Failed to restore original for media [$media->id]: {$e->getMessage()}");

            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.could_not_restore_original')
            );
        }
    }
}
