<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
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

        if (! $file) {
            return MediaResponse::error($request, $initiatorId, __('media-library-extensions::messages.upload_no_files'));
        }

        $directory = "{$basePath}";

        $originalName = $file->getClientOriginalName();
        $safeFilename = sanitizeFilename(pathinfo($originalName, PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $filename = "{$safeFilename}.{$extension}";

        Storage::disk($disk)->putFileAs($directory, $file, $filename);

        $upload = new TemporaryUpload([
            'disk' => $disk,
            'path' => "{$directory}/{$filename}",
            'original_filename' => $originalName,
            'mime_type' => $file->getMimeType(),
            'user_id' => Auth::check() ? Auth::id() : null,
            'session_id' => $request->session()->getId(),
            'order_column' => 1,
            'extra_properties' => [
                'image_collection' => $request->input('image_collection'),
                'document_collection' => $request->input('document_collection'),
                'youtube_collection' => $request->input('youtube_collection'),
            ],
        ]);

        try {
            $upload->save();
        } catch (QueryException $e) {
            // Check if it's a "table not found" error (MySQL / SQLite / etc.)
            if (str_contains($e->getMessage(), 'mle_temporary_uploads')) {
                return MediaResponse::error(
                    $request,
                    $initiatorId,
                    __('media-library-extensions::messages.Temporary_uploads_not_available,_please_run_migrations_first'),
                );
            }

            // Re-throw for other unexpected DB errors
            throw $e;
        }

        return MediaResponse::success(
            $request,
            $initiatorId,
            __('media-library-extensions::messages.upload_success'),
            ['saved_file' => $filename]
        );

    }
}
