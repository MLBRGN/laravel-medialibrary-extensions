<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature\Support;

use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

class DeniedDeleteBlog extends Blog
{
    public static function allowsMediaDeletes(): bool
    {
        return false;
    }

    public function getTable()
    {
        return 'blogs';
    }
}
