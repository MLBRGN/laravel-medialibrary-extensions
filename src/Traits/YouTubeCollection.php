<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Trait HandlesMediaConversions
 *
 * Defines media conversion methods for generating responsive images
 * with specific aspect ratios and optimized formats.
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
