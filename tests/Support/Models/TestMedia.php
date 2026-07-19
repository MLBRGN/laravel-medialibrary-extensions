<?php

declare(strict_types=1);

namespace Mlbrgn\MediaLibraryExtensions\Tests\Support\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class TestMedia extends BaseMedia
{
    /**
     * In tests, return the path relative to the disk root so Storage::exists($path)
     * works reliably with the local adapter.
     */
    public function getPath(string $conversionName = ''): string
    {
        if ($conversionName !== '') {
            return $this->getPathRelativeToRoot($conversionName);
        }

        return $this->getPathRelativeToRoot();
    }
}
