<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Mlbrgn\MediaLibraryExtensions\Data\PreparedUpload;
use Mlbrgn\MediaLibraryExtensions\Exceptions\UploadException;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreSingleRequest;

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
}
