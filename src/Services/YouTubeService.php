<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Spatie\MediaLibrary\HasMedia;

class YouTubeService
{
    public function uploadThumbnailFromUrl(HasMedia $model, string $youtubeUrl, string $collection, ?string $customId = null): void
    {
        $videoId = extractYouTubeId($youtubeUrl);

        // TODO: validate $videoId if needed
        $thumbnailUrl = "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";

        $model
            ->addMediaFromUrl($thumbnailUrl)
            ->usingFileName('youtube-thumbnail-' . ($customId ?? $videoId) . '.jpg')
            ->withCustomProperties([
                'youtube-url' => $youtubeUrl,
                'youtube-id' => $videoId,
            ])
            ->toMediaCollection($collection);
    }
}
