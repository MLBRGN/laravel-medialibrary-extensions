<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature;

use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;

class HasMediaExtendedTest extends TestCase
{
    public function test_blog_model_implements_has_media_extended()
    {
        $blog = new Blog;

        $this->assertInstanceOf(HasMediaExtended::class, $blog);
    }

    public function test_it_can_check_if_model_implements_interface_using_is_subclass_of()
    {
        $this->assertTrue(is_subclass_of(Blog::class, HasMediaExtended::class));
    }

    public function test_it_has_expected_methods_from_trait()
    {
        $blog = new Blog;

        $this->assertTrue(method_exists($blog, 'allowsMediaUploads'));
        $this->assertTrue(method_exists($blog, 'allowsMediaUploadFrom'));
        $this->assertTrue(method_exists($blog, 'allowedMediaCollections'));

        $this->assertTrue(Blog::allowsMediaUploads());
        $this->assertTrue($blog->allowsMediaUploadFrom(null));
        $this->assertIsArray($blog->allowedMediaCollections());
    }
}
