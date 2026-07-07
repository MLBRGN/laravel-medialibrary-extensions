<?php

use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

it('blog model implements has media extended', function () {
    $blog = new Blog;

    expect($blog)
        ->toBeInstanceOf(HasMediaExtended::class);
});

it('can check if model implements interface using is_subclass_of', function () {
    expect(is_subclass_of(Blog::class, HasMediaExtended::class))
        ->toBeTrue();
});

it('has expected methods from trait', function () {
    $blog = new Blog;

    expect(method_exists($blog, 'allowsMediaUploads'))
        ->toBeTrue();

    expect(method_exists($blog, 'allowsMediaUploadFrom'))
        ->toBeTrue();

    expect(method_exists($blog, 'allowedMediaCollections'))
        ->toBeTrue();

    expect(Blog::allowsMediaUploads())
        ->toBeTrue();

    expect($blog->allowsMediaUploadFrom(null))
        ->toBeTrue();

    expect($blog->allowedMediaCollections())
        ->toBeArray();
});
