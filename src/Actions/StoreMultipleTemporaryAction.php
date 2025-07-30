<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
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
        $files = $request->file($field);

        if (!Schema::hasTable('mle_temporary_uploads')) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                __('media-library-extensions::messages.Temporary_uploads_not_available,_please_run_migrations_first'),
            );
        }

        if (empty($files)) {
            return MediaResponse::error($request, $initiatorId, __('media-library-extensions::messages.upload_no_files'));
        }

        $directory = "{$basePath}";

        $savedFiles = collect($files)->map(function ($file) use ($disk, $directory, $request) {
            $originalName = $file->getClientOriginalName();
            $safeFilename = sanitizeFilename(pathinfo($originalName, PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();
            $filename = "{$safeFilename}.{$extension}";

            // Store file
            Storage::disk($disk)->putFileAs($directory, $file, $filename);

            $sessionId = $request->session()->getId();

            $maxOrderColumn = TemporaryUpload::where('session_id', $sessionId)->max('order_column') ?? 0;
            $nextOrder = $maxOrderColumn + 1;

            // Create DB record
            TemporaryUpload::create([
                'disk' => $disk,
                'path' => "{$directory}/{$filename}",
                'original_filename' => $originalName,
                'mime_type' => $file->getMimeType(),
                'user_id' => Auth::check() ? Auth::id() : null,
                'session_id' => $request->session()->getId(),
                'order_column' => $nextOrder,
                'extra_properties' => [
                    'image_collection' => $request->input('image_collection'),
                    'document_collection' => $request->input('document_collection'),
                    'youtube_collection' => $request->input('youtube_collection'),
                ],
            ]);

            return $filename;
        })->all();

        return MediaResponse::success(
            $request,
            $initiatorId,
            __('media-library-extensions::messages.upload_success'),
            ['saved_files' => $savedFiles]
        );
    }

}
