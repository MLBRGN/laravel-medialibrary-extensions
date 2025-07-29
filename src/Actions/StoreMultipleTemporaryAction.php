<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\YouTubeService;

class StoreMultipleTemporaryAction
{
    public function __construct(
        protected MediaService $mediaService,
        protected YouTubeService $youTubeService
    ) {}

    public function execute(MediaManagerUploadMultipleRequest $request): RedirectResponse|JsonResponse
    {
        $field = config('media-library-extensions.upload_field_name_multiple');
        $disk = config('media-library-extensions.temporary_upload_disk');
        $basePath = config('media-library-extensions.temporary_upload_path');
        $initiatorId = $request->initiator_id;
        $temporaryUploadUuid = $request->temporary_upload_uuid;
        $files = $request->file($field);

        if (empty($files)) {
            return MediaResponse::error($request, $initiatorId, __('media-library-extensions::messages.upload_no_files'));
        }

        $directory = "{$basePath}/{$temporaryUploadUuid}";

        $savedFiles = collect($files)->map(function ($file) use ($disk, $directory, $temporaryUploadUuid) {
            $originalName = $file->getClientOriginalName();
            $safeFilename = sanitizeFilename(pathinfo($originalName, PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();
            $filename = "{$safeFilename}.{$extension}";

            Storage::disk($disk)->putFileAs($directory, $file, $filename);

            return $filename;
        })->all();

        return MediaResponse::success(
            $request,
            $initiatorId,
            __('media-library-extensions::messages.upload_success'),
            ['temporary_upload_uuid' => $temporaryUploadUuid, 'saved_files' => $savedFiles]
        );
    }

}
