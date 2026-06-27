<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature\Support;

use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

class CollectionRestrictedBlog extends Blog
{
    public function allowedMediaCollections(): array
    {
        return ['allowed-collection'];
    }

    public function getTable()
    {
        return 'blogs';
    }
}
