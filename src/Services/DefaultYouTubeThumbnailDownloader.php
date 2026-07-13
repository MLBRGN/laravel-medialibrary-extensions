<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Illuminate\Support\Facades\Http;
use Mlbrgn\MediaLibraryExtensions\Interfaces\YouTubeThumbnailDownloader;

class DefaultYouTubeThumbnailDownloader implements YouTubeThumbnailDownloader
{
    public function download(string $youtubeId): ?string
    {
        $baseUrl = "https://img.youtube.com/vi/{$youtubeId}";

        // Fast → medium → low → best (optional)
        $candidates = [
            "{$baseUrl}/maxresdefault.jpg", // slow / often missing, but nice quality
            "{$baseUrl}/hqdefault.jpg",
            "{$baseUrl}/mqdefault.jpg",
            "{$baseUrl}/default.jpg",
        ];

        foreach ($candidates as $url) {
            try {
                $response = Http::timeout(1.5)
                    ->connectTimeout(1)
                    ->get($url);

                if (! $response->successful()) {
                    continue;
                }

                $body = $response->body();

                // basic validation: avoid saving HTML error pages
                if (str_starts_with(trim($body), '<')) {
                    continue;
                }

                $path = tempnam(sys_get_temp_dir(), 'youtube-thumbnail-');

                file_put_contents($path, $body);

                return $path;

            } catch (\Throwable $e) {
                // skip and try next
                continue;
            }
        }

        return null;
    }
}
