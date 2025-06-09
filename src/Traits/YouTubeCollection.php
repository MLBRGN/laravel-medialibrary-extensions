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

    protected function addYouTubeCollection($name) {
        $this
            ->addMediaCollection($name)
            ->singleFile();
    }

}
