<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

class StoreSingleTemporaryAction
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function execute(MediaManagerUploadSingleRequest $request): RedirectResponse|JsonResponse
    {
        $field = config('media-library-extensions.upload_field_name_single');
        $disk = config('media-library-extensions.temporary_upload_disk');
        $basePath = config('media-library-extensions.temporary_upload_path');
        $initiatorId = $request->initiator_id;
        $file = $request->file($field);

        if (!Schema::hasTable('mle_temporary_uploads')) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                __('media-library-extensions::messages.Temporary_uploads_not_available,_please_run_migrations_first'),
            );
        }

        if (! $file) {
            return MediaResponse::error($request, $initiatorId, __('media-library-extensions::messages.upload_no_files'));
        }

        $directory = "{$basePath}";

        $originalName = $file->getClientOriginalName();
        $safeFilename = sanitizeFilename(pathinfo($originalName, PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $filename = "{$safeFilename}.{$extension}";

        Storage::disk($disk)->putFileAs($directory, $file, $filename);

        return MediaResponse::success(
            $request,
            $initiatorId,
            __('media-library-extensions::messages.upload_success'),
            ['saved_file' => $filename]
        );

    }
}
