<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

class DeniedEditBlog extends Blog
{
    public function allowsMediaEditsFrom(?Authenticatable $user): bool
    {
        return false;
    }

    public function getTable()
    {
        return 'blogs';
    }
}
