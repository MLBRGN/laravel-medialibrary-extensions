<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadSingleRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class StoreSingleTemporaryAction
{
    use ChecksMediaLimits;

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
            return MediaResponse::error(
                $request,
                $initiatorId,
                __('media-library-extensions::messages.upload_no_files')
            );
        }

        $collections = collect([
            $request->input('image_collection'),
            $request->input('document_collection'),
            $request->input('youtube_collection'),
            $request->input('video_collection'),
            $request->input('audio_collection'),
        ])->filter()->all();

        if ($this->temporaryUploadsHaveAnyMedia($collections)) {
            return MediaResponse::error(
                $request,
                $request->initiator_id,
                __('media-library-extensions::messages.only_one_medium_allowed')
            );
        }

        $originalName = $file->getClientOriginalName();
        $mimetype = $file->getMimeType();
        $collection = $this->mediaService->determineCollection($file);

        if (is_null($collection)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype_:mimetype', ['mimetype' => $mimetype]),
                [
                    'skipped_file' => [
                        'filename' => $originalName,
                        'reason' => __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype_:mimetype', ['mimetype' => $mimetype]),
                    ],
                ]
            );
        }

        $sessionId = $request->session()->getId();
        $userId = Auth::check() ? Auth::id() : null;

        // Remove existing upload for this session/user
        $existing = TemporaryUpload::query()
            ->where('session_id', $sessionId)
            ->when($userId, fn ($q) => $q->orWhere('user_id', $userId))
            ->first();

        if ($existing) {
            Storage::disk($existing->disk)->delete($existing->path);
            $existing->delete();
        }

        // Save the new file
        $safeFilename = sanitizeFilename(pathinfo($originalName, PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $filename = "{$safeFilename}.{$extension}";
        $directory = "{$basePath}";

        Storage::disk($disk)->putFileAs($directory, $file, $filename);

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

        return MediaResponse::success(
            $request,
            $initiatorId,
            __('media-library-extensions::messages.upload_success'),
            ['saved_file' => $filename]
        );
    }
}
