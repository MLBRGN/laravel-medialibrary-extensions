<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Illuminate\Database\Eloquent\Model;

class YouTubeUploadService
{
    public function uploadFromUrl(Model $model, string $url, string $collection): void
    {
        $videoId = extractYouTubeId($url);
        $thumbnailUrl = "https://img.youtube.com/vi/$videoId/maxresdefault.jpg";

        $model->addMediaFromUrl($thumbnailUrl)
            ->usingFileName("youtube-thumbnail-$videoId.jpg")
            ->withCustomProperties([
                'youtube-url' => $url,
                'youtube-id' => $videoId,
            ])
            ->toMediaCollection($collection);
    }
}
