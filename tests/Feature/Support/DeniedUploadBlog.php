<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature\Support;

use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

class DeniedUploadBlog extends Blog
{
    public static function allowsMediaUploads(): bool
    {
        return false;
    }

    public function getTable()
    {
        return 'blogs';
    }
}
