<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Trait YouTubeCollection
 *
 * Defines YouTubeCollection
 */
trait YouTubeCollection
{

    use InteractsWithMedia;

    protected function addYouTubeCollection($name): void {
        $this
            ->addMediaCollection($name)
            ->singleFile();
    }

}
