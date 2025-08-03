<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function getConnectionName()
    {
        if (config('media-library-extensions.demo_pages_enabled') && \Mlbrgn\MediaLibraryExtensions\Helpers\DemoHelper::isRequestFromDemoPage()) {
            return config('media-library-extensions.temp_database_name');
        }

        return parent::getConnectionName();
    }

}
