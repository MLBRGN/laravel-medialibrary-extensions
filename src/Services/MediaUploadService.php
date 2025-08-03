<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class MediaUploadService
{
    public function uploadToModel(Model $model, UploadedFile $file, string $imageCollection, string $documentCollection): ?string
    {
        $mimeType = $file->getMimeType();

        if (in_array($mimeType, config('media-library-extensions.allowed_mimetypes.image'))) {
            $collection = $imageCollection;
        } elseif (in_array($mimeType, config('media-library-extensions.allowed_mimetypes.document'))) {
            $collection = $documentCollection;
        } else {
            return null;
        }

        $model->addMedia($file)->toMediaCollection($collection);

        return $collection;
    }
}
