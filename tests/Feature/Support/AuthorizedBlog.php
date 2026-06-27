<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

class AuthorizedBlog extends Blog
{
    public function allowsMediaUploadFrom(?Authenticatable $user): bool
    {
        return $user && $user->getAuthIdentifier() === 1;
    }

    public function allowsMediaDeletesFrom(?Authenticatable $user): bool
    {
        return $user && $user->getAuthIdentifier() === 1;
    }

    public function allowsMediaEditsFrom(?Authenticatable $user): bool
    {
        return $user && $user->getAuthIdentifier() === 1;
    }

    public function getTable()
    {
        return 'blogs';
    }
}
