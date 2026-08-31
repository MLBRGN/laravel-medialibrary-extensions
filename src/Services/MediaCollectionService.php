<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

class MediaCollectionService
{
    public function __construct(
        protected DataSourceResolver $dataSourceResolver,
    ) {}

    public function determineCollectionType($file): ?string
    {
        $mimeType = $file->getMimeType();

        if (in_array($mimeType, config('medialibrary-extensions.allowed_mimetypes.image'))) {
            return 'image';
        }

        if (in_array($mimeType, config('medialibrary-extensions.allowed_mimetypes.document'))) {
            return 'document';
        }

        if (in_array($mimeType, config('medialibrary-extensions.allowed_mimetypes.audio'))) {
            return 'audio';
        }

        if (in_array($mimeType, config('medialibrary-extensions.allowed_mimetypes.video'))) {
            return 'video';
        }

        return null;
    }
}
