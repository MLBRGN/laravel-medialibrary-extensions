<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Fakes;

use Mlbrgn\MediaLibraryExtensions\Interfaces\YouTubeThumbnailDownloader;

class FakeYouTubeThumbnailDownloader implements YouTubeThumbnailDownloader
{
    public function download(string $youtubeId): ?string
    {
        $source = __DIR__ . '/../Fixtures/test.jpg';

        $temp = tempnam(sys_get_temp_dir(), 'yt-thumb-');
        copy($source, $temp);

        return $temp;
    }
}
