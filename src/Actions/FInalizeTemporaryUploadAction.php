<?php

namespace Mlbrgn\MediaLibraryExtensions\Actions;

use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

class FinalizeTemporaryUploadAction
{
    public function __construct(
        protected MediaService $mediaService,
    ) {}

    public function execute(
        string $modelType,
        string|int $modelId,
        string $uuid,
        ?string $collection = null,
        ?string $disk = null,
        ?string $tempPathBase = null
    ): bool {
        $disk = $disk ?? config('media-library-extensions.temporary_upload_disk');
        $tempPathBase = $tempPathBase ?? config('media-library-extensions.temporary_upload_path');
        $filePath = "{$tempPathBase}/{$uuid}";

        $storage = Storage::disk($disk);
        $fullPath = $storage->path($filePath);

        if (! $storage->exists("{$uuid}") && ! $storage->exists($filePath)) {
            throw new \RuntimeException("Temporary file [{$filePath}] not found on disk [{$disk}]");
        }

        $model = $this->mediaService->resolveModel($modelType, $modelId);

        try {
            $media = $model
                ->addMedia($fullPath)
                ->usingFileName(basename($filePath))
                ->toMediaCollection($collection ?? 'default');
        } catch (FileCannotBeAdded $e) {
            report($e);
            return false;
        }

        // Optional cleanup
        $storage->delete($filePath);

        return true;
    }
}
