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
        $mime = $file->getMimeType();

        if (in_array($mime, config('media-library-extensions.allowed_mimetypes.image'))) {
            return request()->input('image_collection');
        }

        if (in_array($mime, config('media-library-extensions.allowed_mimetypes.document'))) {
            return request()->input('document_collection');
        }

        return null;
    }
}
