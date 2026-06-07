<?php

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait ResolveMediaComponent
{
    /**
     * Resolve the Blade component name for a given medium.
     */
    public function resolveComponentForMedium(?Media $medium): ?string
    {
        if (! $medium) {
            return null;
        }

        // optional: fetch from config so it’s customizable
        $map = config('medialibrary-extensions.component_map', [
            'youtube-video' => 'mle-video-youtube',
            'document' => 'mle-document',
            'video' => 'mle-video',
            'audio' => 'mle-audio',
            'image' => 'mle-image-responsive',
        ]);

        $type = getMediaType($medium);

        return $map[$type] ?? null;
    }
}
