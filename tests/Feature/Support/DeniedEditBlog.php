<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
class DeniedEditBlog extends Blog
{
    public static function allowsMediaEdits(): bool
    {
        return false;
    }

    public function getTable()
    {
        return 'blogs';
    }
}
