<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Support\classes;

use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\User;

class TestBlogPolicy
{
    public function uploadMedia(?User $user, Blog $blog): bool
    {
        return $user?->id === 1;
    }

    public function editMedia(?User $user, Blog $blog): bool
    {
        return $user?->id === 1;
    }

    public function deleteMedia(?User $user, Blog $blog): bool
    {
        return $user?->id === 1;
    }
}
