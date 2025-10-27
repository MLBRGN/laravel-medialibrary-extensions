<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Spatie\MediaLibrary\HasMedia;

class MediaService
{
    public function resolveModel(string $modelType, string $modelId): HasMedia
    {
        abort_unless(class_exists($modelType), 400, 'Invalid model type');

        return $modelType::findOrFail($modelId);
    }

    public function determineCollectionType($file): ?string
    {
        $mimeType = $file->getMimeType();

        if (in_array($mimeType, config('media-library-extensions.allowed_mimetypes.image'))) {
            return 'image';
        }

        if (in_array($mimeType, config('media-library-extensions.allowed_mimetypes.document'))) {
            return 'document';
        }

        if (in_array($mimeType, config('media-library-extensions.allowed_mimetypes.audio'))) {
            return 'audio';
        }

        if (in_array($mimeType, config('media-library-extensions.allowed_mimetypes.video'))) {
            return 'video';
        }

        return null; // means not supported
    }
}
