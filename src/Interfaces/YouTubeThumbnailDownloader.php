<?php

namespace Mlbrgn\MediaLibraryExtensions\Interfaces;

interface YouTubeThumbnailDownloader
{
    public function download(string $youtubeId): ?string;
}
