<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class YouTubeService
{
    public function uploadThumbnailFromUrl(
        HasMedia $model,
        string $youtubeUrl,
        string $collection,
        ?string $customId = null
    ): ?Media {
        $videoId = extractYouTubeId($youtubeUrl);

        // TODO: validate $videoId if needed
        $thumbnailUrl = "https://img.youtube.com/vi/$videoId/maxresdefault.jpg";

        try {
            return $model
                ->addMediaFromUrl($thumbnailUrl)
                ->usingFileName('youtube-thumbnail-'.($customId ?? $videoId).'.jpg')
                ->withCustomProperties([
                    'youtube-url' => $youtubeUrl,
                    'youtube-id' => $videoId,
                ])
                ->toMediaCollection($collection);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function storeTemporaryThumbnailFromRequest(StoreYouTubeVideoRequest $request): ?TemporaryUpload
    {
        $youtubeUrl = $request->input('youtube_url');
        $youtubeId = $request->input('youtube_id');
        $collection = $request->input('youtube_collection');
        $sessionId = $request->session()->getId();

        //        dd($youtubeUrl, $youtubeId, $collection, $sessionId);
        // Todo look at this
        return $this->storeTemporaryThumbnailFromUrl(
            youtubeUrl: $youtubeUrl,
            sessionId: $sessionId,
            customId: $youtubeId,
            collection: $collection,
        );
    }

    public function storeTemporaryThumbnailFromUrl(
        string $youtubeUrl,
        string $sessionId,
        ?string $customId = null,
        ?string $collection = null
    ): ?TemporaryUpload {
        $disk = config('media-library-extensions.media_disks.temporary');
        $basePath = '';
        $videoId = $customId ?? extractYouTubeId($youtubeUrl);
        if (! $videoId) {
            return null;
        }

        $thumbnailUrl = "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
        $contents = @file_get_contents($thumbnailUrl);

        if (! $contents) {
            return null;
        }

        $filename = sanitizeFilename("youtube-{$videoId}.jpg");
        $fullPath = "{$basePath}/{$filename}";

        Storage::disk($disk)->put($fullPath, $contents);
        $mimeType = Storage::disk($disk)->mimeType($fullPath);
        $size = Storage::disk($disk)->size($fullPath);

        $maxOrder = TemporaryUpload::where('session_id', $sessionId)->max('order_column') ?? 0;

        return TemporaryUpload::create([
            'disk' => $disk,
            'path' => $fullPath,
            'name' => $filename,
            'file_name' => $filename,
            'size' => $size,
            'collection_name' => $collection ?? 'workplace-youtube-videos',
            'mime_type' => $mimeType,
            'user_id' => Auth::id(),
            'session_id' => $sessionId,
            'order_column' => $maxOrder + 1,
            'custom_properties' => [
                'youtube-url' => $youtubeUrl,
                'youtube-id' => $videoId,
            ],
        ]);
    }
}
