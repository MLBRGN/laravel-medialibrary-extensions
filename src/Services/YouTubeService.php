<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;
use Mlbrgn\MediaLibraryExtensions\Interfaces\YouTubeThumbnailDownloader;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class YouTubeService
{
    public function __construct(
        protected DataSourceResolver $dataSourceResolver,
        protected YouTubeThumbnailDownloader $thumbnailDownloader,
    ) {}

    public function uploadThumbnailFromUrl(
        HasMedia $model,
        string $youtubeUrl,
        string $collection,
        ?string $customId = null,
        ?string $dataSource = 'default'
    ): ?Media {
        $videoId = extractYouTubeId($youtubeUrl);

        // TODO: validate $videoId if needed
        $thumbnailUrl = "https://img.youtube.com/vi/$videoId/maxresdefault.jpg";

        $modelInstance = $model;
        if ($dataSource) {
            $connection = $this->dataSourceResolver->resolveConnection($dataSource);
            $modelInstance->setConnection($connection);
        }

        try {
            $filePath = $this->thumbnailDownloader->download($videoId);

            if (! $filePath) {
                return null;
            }

            return $modelInstance
                ->addMedia($filePath)
                ->usingFileName('youtube-thumbnail-'.($customId ?? $videoId).'.jpg')
                ->withCustomProperties([
                    'youtube-url' => $youtubeUrl,
                    'youtube-id' => $videoId,
                ])
                ->toMediaCollection($collection);
//            return $modelInstance
//                ->addMediaFromUrl($thumbnailUrl)
//                ->usingFileName('youtube-thumbnail-'.($customId ?? $videoId).'.jpg')
//                ->withCustomProperties([
//                    'youtube-url' => $youtubeUrl,
//                    'youtube-id' => $videoId,
//                ])
//                ->toMediaCollection($collection);
        } catch (\Exception $e) {
            Log::error('YouTube thumbnail failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    public function storeTemporaryThumbnailFromRequest(StoreYouTubeVideoRequest $request): ?TemporaryUpload
    {
        $youtubeUrl = $request->input('youtube_url');
        $youtubeId = $request->input('youtube_id');
        $collection = $request->input('youtube_collection');
        $clientToken = $request->input('client_token')
            ?? $request->cookie('mle_client_token')
            ?? (string) Str::ulid();
        $dataSource = $request->input('data_source');
        $instanceId = $request->input('instance_id');

        return $this->storeTemporaryThumbnailFromUrl(
            youtubeUrl: $youtubeUrl,
            clientToken: $clientToken,
            customId: $youtubeId,
            collection: $collection,
            dataSource: $dataSource,
            instanceId: $instanceId
        );
    }

    public function storeTemporaryThumbnailFromUrl(
        string $youtubeUrl,
        string $clientToken,
        ?string $customId = null,
        ?string $collection = null,
        ?string $dataSource = 'default',
        ?string $instanceId = null
    ): ?TemporaryUpload {
        $disk = config('medialibrary-extensions.media_disks.temporary');
        $basePath = '';
        $videoId = $customId ?? extractYouTubeId($youtubeUrl);
        if (! $videoId) {
            return null;
        }

        $filePath = $this->thumbnailDownloader->download($videoId);

        if (! $filePath) {
            return null;
        }

        $filename = sanitizeFilename("youtube-{$videoId}.jpg");
        $fullPath = "{$basePath}/{$filename}";
        $contents = file_get_contents($filePath);

        Storage::disk($disk)->put($fullPath, $contents);
//        $mimeType = Storage::disk($disk)->mimeType($fullPath);
//        $size = Storage::disk($disk)->size($fullPath);
        $size = strlen($contents);
        $mimeType = 'image/jpeg';
//        unlink($filePath); // TODO

        $temporaryUploadModel = new TemporaryUpload;
        $connection = $this->dataSourceResolver->resolveConnection($dataSource);
        $temporaryUploadModel->setConnection($connection);

        $maxOrder = $temporaryUploadModel->newQuery()->where('client_token', $clientToken)->max('order_column') ?? 0;

        $tempUpload = $temporaryUploadModel->newQuery()->create([
            'disk' => $disk,
            'path' => $fullPath,
            'name' => $filename,
            'file_name' => $filename,
            'size' => $size,
            'collection_name' => $collection ?? 'workplace-youtube-videos',
            'mime_type' => $mimeType,
            'user_id' => Auth::id(),
            'client_token' => $clientToken,
            'instance_id' => $instanceId,
            'order_column' => $maxOrder + 1,
            'custom_properties' => [
                'youtube-url' => $youtubeUrl,
                'youtube-id' => $videoId,
            ],
        ]);

//        Log::info('storeTemporaryThumbnailFromUrl success', [
//            'id' => $tempUpload->id,
//            'collection' => $tempUpload->collection_name,
//            'instance_id' => $tempUpload->instance_id,
//        ]);

        return $tempUpload;
    }
}
