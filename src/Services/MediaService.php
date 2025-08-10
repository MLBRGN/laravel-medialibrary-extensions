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

    public function determineCollection($file): ?string
    {
        $mimeType = $file->getMimeType();

        if (in_array($mimeType, config('media-library-extensions.allowed_mimetypes.image'))) {
            return request()->input('image_collection');
        }

        if (in_array($mimeType, config('media-library-extensions.allowed_mimetypes.document'))) {
            return request()->input('document_collection');
        }

        if (in_array($mimeType, config('media-library-extensions.allowed_mimetypes.audio'))) {
            return request()->input('audio_collection');
        }

        if (in_array($mimeType, config('media-library-extensions.allowed_mimetypes.video'))) {
            return request()->input('video_collection');
        }


        return null; // means not supported
    }
}
