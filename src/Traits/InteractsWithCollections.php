<?php

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Illuminate\Support\Collection;

trait InteractsWithCollections
{
    public function mergeCollections($collections): array
    {

        // define default collection names
        return array_merge([
            'image' => '',
            'document' => '',
            'youtube' => '',
            'video' => '',
            'audio' => '',
        ], $collections);
    }
}
