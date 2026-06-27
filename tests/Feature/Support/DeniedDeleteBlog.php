<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

class DeniedDeleteBlog extends Blog
{
    public function allowsMediaDeletesFrom(?Authenticatable $user): bool
    {
        return false;
    }

    public function getTable()
    {
        return 'blogs';
    }
}
