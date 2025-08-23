<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadMultipleRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;

class StoreMultipleTemporaryAction
{
    use ChecksMediaLimits;

    public function __construct(
        protected MediaService $mediaService,
    ) {}

    public function execute(MediaManagerUploadMultipleRequest $request): RedirectResponse|JsonResponse
    {
        $field = config('media-library-extensions.upload_field_name_multiple');
        $disk = config('media-library-extensions.temporary_upload_disk');
        $basePath = config('media-library-extensions.temporary_upload_path');
        $initiatorId = $request->initiator_id;
        $mediaManagerId = $request->media_manager_id;// non-xhr needs media-manager-id, xhr relies on initiatorId

        $files = $request->file($field);

        if (empty($files)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.upload_no_files')
            );
        }

        $collections = collect([
            $request->input('image_collection'),
            $request->input('document_collection'),
            $request->input('youtube_collection'),
            $request->input('video_collection'),
            $request->input('audio_collection'),
        ])->filter()->all();// remove falsy values

        $maxItemsInCollection = config('media-library-extensions.max_items_in_shared_media_collections');
        $temporaryUploadsInCollections = $this->countTemporaryUploadsInCollections($collections);
        $nextPriority = $temporaryUploadsInCollections;
        if ($temporaryUploadsInCollections >= $maxItemsInCollection) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.this_collection_can_contain_up_to_:items_items', [
                    'items' => $maxItemsInCollection
                ])
            );
        }

        $directory = "{$basePath}";
        $sessionId = $request->session()->getId();

        $savedFiles = [];
        $skippedFiles = [];

        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            $collection = $this->mediaService->determineCollection($file);

            if (is_null($collection)) {
                $skippedFiles[] = [
                    'filename' => $originalName,
                    'reason' => __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype_:mimetype', [
                        'mimetype' => $file->getMimeType(),
                    ]),
                ];

                continue;
            }

            $safeFilename = sanitizeFilename(pathinfo($originalName, PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();
            $filename = "{$safeFilename}.{$extension}";

            // Store file
            Storage::disk($disk)->putFileAs($directory, $file, $filename);

            // Create DB record
            $upload = new TemporaryUpload([
                'disk' => $disk,
                'path' => "{$directory}/{$filename}",
                'name' => $safeFilename,
                'file_name' => $originalName,
                'collection_name' => $collection,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'user_id' => Auth::check() ? Auth::id() : null,
                'session_id' => $sessionId,
                'order_column' => $nextPriority,
                'custom_properties' => [
                    'image_collection' => $request->input('image_collection'),
                    'document_collection' => $request->input('document_collection'),
                    'youtube_collection' => $request->input('youtube_collection'),
                    'video_collection' => $request->input('video_collection'),
                    'audio_collection' => $request->input('audio_collection'),
                    'priority' => $nextPriority,
                ],
            ]);

            $nextPriority++;

            $upload->save();
            $savedFiles[] = $filename;
        }

        if (empty($savedFiles)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
                $mediaManagerId,
                __('media-library-extensions::messages.upload_failed_due_to_invalid_mimetype'),
            );
        }

        $messageExtra = '';
        foreach ($skippedFiles as $skippedFile) {
            $messageExtra .= '"'.$skippedFile['filename'].'":  '.$skippedFile['reason'].',';
        }

        return MediaResponse::success(
            $request,
            $initiatorId,
            $mediaManagerId,
            __('media-library-extensions::messages.upload_success'),
            [
                'message_extra' => $messageExtra,
                'saved_files' => $savedFiles,
                'skipped_files' => $skippedFiles,
            ]
        );
    }
}
