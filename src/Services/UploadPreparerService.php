<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Mlbrgn\MediaLibraryExtensions\Data\PreparedUpload;
use Mlbrgn\MediaLibraryExtensions\Exceptions\UploadException;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;
use Illuminate\Http\UploadedFile;

class UploadPreparerService
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function prepareSingleUpload(
        StoreSingleRequest $request
    ): PreparedUpload {
        $file = $request->file('media');

        if (! $file) {
            throw new UploadException(
                __('medialibrary-extensions::messages.upload_no_files')
            );
        }

        $maxUploadSize = (int) config('medialibrary-extensions.max_upload_size');

        if ($file->getSize() > $maxUploadSize) {
            throw new UploadException(
                __('medialibrary-extensions::messages.file_too_large', [
                    'file' => $file->getClientOriginalName(),
                    'max' => $maxUploadSize,
                ])
            );
        }

        $collections = $request->array('collections');

        if (empty($collections)) {
            throw new UploadException(
                __('medialibrary-extensions::messages.no_media_collections')
            );
        }

        $collectionType = $this->mediaService
            ->determineCollectionType($file);

        if (! $collectionType) {
            throw new UploadException(
                __('medialibrary-extensions::messages.upload_failed_due_to_invalid_mimetype')
            );
        }

        $collectionName = $collections[$collectionType] ?? null;

        if (! $collectionName) {
            throw new UploadException(
                __('medialibrary-extensions::messages.upload_failed_due_to_invalid_collection')
            );
        }

        return new PreparedUpload(
            file: $file,
            collectionType: $collectionType,
            collectionName: $collectionName,
            collections: $collections,
            originalName: $file->getClientOriginalName(),
            mimeType: $file->getMimeType(),
            size: $file->getSize(),
        );
    }

    /**
     * Prepare multiple uploads using the same validation and mapping rules as single uploads.
     *
     * @param UploadedFile[] $files
     * @param array<string, string> $collections
     * @return array{prepared: PreparedUpload[], errors: list<string>, failedFilenames: list<string>}
     */
    public function prepareMultipleUploads(array $files, array $collections): array
    {
        $prepared = [];
        $errors = [];
        $failed = [];

        if (empty($files)) {
            // Mirror single upload behavior for empty set at call site; here we just return no prepared
            return compact('prepared', 'errors', 'failed');
        }

        if (empty($collections)) {
            // Let caller handle global "no collections" error to keep response shape consistent
            return compact('prepared', 'errors', 'failed');
        }

        $maxUploadSize = (int) config('medialibrary-extensions.max_upload_size');

        foreach ($files as $file) {
            // Size check
            if ($file->getSize() > $maxUploadSize) {
                $failed[] = $file->getClientOriginalName();
                $errors[] = __('medialibrary-extensions::messages.file_too_large', [
                    'file' => $file->getClientOriginalName(),
                    'max' => $maxUploadSize,
                ]);
                continue;
            }

            // Determine type
            $collectionType = $this->mediaService->determineCollectionType($file);
            if (! $collectionType) {
                $failed[] = $file->getClientOriginalName();
                $errors[] = __('medialibrary-extensions::messages.upload_failed_due_to_invalid_mimetype', [
                    'file' => $file->getClientOriginalName(),
                ]);
                continue;
            }

            // Map to collection name
            $collectionName = $collections[$collectionType] ?? null;
            if (! $collectionName) {
                $failed[] = $file->getClientOriginalName();
                $errors[] = __('medialibrary-extensions::messages.invalid_or_missing_collection', [
                    'file' => $file->getClientOriginalName(),
                ]);
                continue;
            }

            $prepared[] = new PreparedUpload(
                file: $file,
                collectionType: $collectionType,
                collectionName: $collectionName,
                collections: $collections,
                originalName: $file->getClientOriginalName(),
                mimeType: $file->getMimeType(),
                size: $file->getSize(),
            );
        }

        return [
            'prepared' => $prepared,
            'errors' => $errors,
            'failedFilenames' => $failed,
        ];
    }
}
