<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Models\demo\DemoMedia;
use Mlbrgn\MediaLibraryExtensions\Models\demo\DemoTemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaService
{

    public function resolveModel(
        string $modelType,
        string|int $modelId
    ): HasMediaExtended {

        abort_unless(class_exists($modelType), 400, 'Invalid model type');

        $model = $modelType::findOrFail($modelId);

        abort_unless(
            $model instanceof HasMediaExtended,
            400,
            'Invalid media model'
        );

        return $model;
    }

    public function resolveMediaModel(
        string|int $mediaId
    ): Media {

        $mediaModelClass = $this->resolveMediaModelClass();

        return $mediaModelClass::findOrFail($mediaId);
    }

    public function resolveMediaModelClass(): string
    {
        if (app()->bound('mle-demo-mode')) {
            return DemoMedia::class;
        }
        return config(
            'media-library.media_model',
            Media::class
        );
    }

    public function resolveTemporaryUploadModel(
        string|int $temporaryUploadId
    ): TemporaryUpload {

        $mediaModelClass = $this->resolveTemporaryUploadModelClass();

        return $mediaModelClass::findOrFail($temporaryUploadId);
    }

    public function resolveTemporaryUploadModelClass(): string
    {
        if (app()->bound('mle-demo-mode')) {
            return DemoTemporaryUpload::class;
        }
        return config(
            'media-library.media_model',
            TemporaryUpload::class
        );
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
