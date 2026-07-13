<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

/*
 * Replaces media
 */

use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GlobalOrderService
{
    public function __construct()
    {
    }


    /**
     * Assign or preserve a global sequential order across all media.
     */
    public function ensureGlobalOrder(Media $media): void
    {
        // Preserve if already set (for replaced or restored media)
        if ($media->hasCustomProperty('global_order')) {
            return;
        }

        // Compute next global order number
        $maxOrder = $this->getMaxGlobalOrder($media->getConnectionName());
        $nextOrder = ((int) $maxOrder) + 1;
        $media->setConnection($media->getConnectionName());
        $media->setCustomProperty('global_order', $nextOrder);
        $media->save();
    }

    /**
     * Helper: safely get max global_order for both MySQL and SQLite.
     */
    private function getMaxGlobalOrder(?string $connection = null): int
    {
        $connection = $connection ?: DB::getDefaultConnection();
        $driver = DB::connection($connection)->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite lacks JSON_UNQUOTE / JSON_EXTRACT
            return (int) Media::on($connection)->get()
                ->map(fn ($m) => (int) $m->getCustomProperty('global_order', 0))
                ->max();
        }

        return (int) Media::on($connection)
            ->selectRaw("MAX(CAST(JSON_UNQUOTE(JSON_EXTRACT(custom_properties, '$.global_order')) AS UNSIGNED)) as max_order")
            ->value('max_order');
    }

}
