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

        if (empty($files)) {
            return MediaResponse::error($request, $initiatorId, __('media-library-extensions::messages.upload_no_files'));
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

            $maxOrderColumn = TemporaryUpload::where('session_id', $sessionId)->max('order_column') ?? 0;
            $nextOrder = $maxOrderColumn + 1;

//            dd($safeFilename);
            // Create DB record
            $upload = new TemporaryUpload([
                'disk' => $disk,
                'path' => "{$directory}/{$filename}",
                'name' => $safeFilename,
                'file_name' => $originalName,
                'collection_name' => $collection,
                'mime_type' => $file->getMimeType(),
                'user_id' => Auth::check() ? Auth::id() : null,
                'session_id' => $sessionId,
                'order_column' => $nextOrder,
                'extra_properties' => [
                    'image_collection' => $request->input('image_collection'),
                    'document_collection' => $request->input('document_collection'),
                    'youtube_collection' => $request->input('youtube_collection'),
                ],
            ]);

            $upload->save();
            $savedFiles[] = $filename;
        }

        // Handle YouTube uploads (if no files, or in addition to files)
//        if ($request->filled('youtube_url')) {
//            $youtubeCollection = $request->input('youtube_collection') ?? 'workplace-youtube-videos';
//            $youtubeId = $request->input('youtube_id');
//            $youtubeUrl = $request->input('youtube_url');
//
//            $thumbnailPath = $this->youTubeService->downloadThumbnail(
//                youtubeUrl: $youtubeUrl,
//                disk: $disk,
//                pathPrefix: $directory,
//                customId: $youtubeId
//            );
//
//            if ($thumbnailPath) {
//                $safeFilename = sanitizeFilename(pathinfo($thumbnailPath, PATHINFO_FILENAME));
//                $originalName = basename($thumbnailPath);
//                $extension = pathinfo($thumbnailPath, PATHINFO_EXTENSION);
//                $mimeType = Storage::disk($disk)->mimeType($thumbnailPath);
//
//                $maxOrderColumn = TemporaryUpload::where('session_id', $sessionId)->max('order_column') ?? 0;
//                $nextOrder = $maxOrderColumn + 1;
//
//                $upload = new TemporaryUpload([
//                    'disk' => $disk,
//                    'path' => $thumbnailPath,
//                    'name' => $safeFilename,
//                    'file_name' => $originalName,
//                    'collection_name' => $youtubeCollection,
//                    'mime_type' => $mimeType,
//                    'user_id' => Auth::check() ? Auth::id() : null,
//                    'session_id' => $sessionId,
//                    'order_column' => $nextOrder,
//                    'extra_properties' => [
//                        'youtube_url' => $youtubeUrl,
//                        'youtube_id' => $youtubeId,
//                        'youtube_collection' => $youtubeCollection,
//                    ],
//                ]);
//
//                $upload->save();
//                $savedFiles[] = $originalName;
//            } else {
//                return MediaResponse::error(
//                    $request, $initiatorId,
//                    __('media-library-extensions::messages.youtube_thumbnail_download_failed')
//                );
//            }
//        }

        if (empty($savedFiles)) {
            return MediaResponse::error(
                $request,
                $initiatorId,
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
            __('media-library-extensions::messages.upload_success'),
            [
                'message_extra' => $messageExtra,
                'saved_files' => $savedFiles,
                'skipped_files' => $skippedFiles,
            ]
        );
    }
}
