<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

// TODO integrate?
class OriginalMedia extends Media
{
    protected $table = 'media'; // same table

    /**
     * Scope to only include media items that are marked as originals.
     */
    public function scopeOnlyOriginals($query)
    {
        return $query->whereJsonContains('custom_properties->is_original', true);
    }

    /**
     * Determine if this media item is an original copy.
     */
    public function isOriginal(): bool
    {
        return (bool) $this->getCustomProperty('is_original', false);
    }

    /**
     * Accessor to the “derived” media that reused this original.
     */
    public function derivedMedia()
    {
        return static::query()
            ->whereJsonContains('custom_properties->original_id', $this->id)
            ->get();
    }
}
