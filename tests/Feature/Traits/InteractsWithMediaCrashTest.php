<?php

use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $disk = config('media-library.disk_name') ?: 'public';
    Storage::fake($disk);
    config(['media-library.queue_conversions_by_default' => false]);
});

it('does not crash when adding a non-image file that triggers responsive conversions', function () {
    // Create a dummy file that is NOT an image
    $dummyPath = __DIR__ . '/../../dummy_not_an_image.txt';
    File::put($dummyPath, 'This is not an image');

    $blog = Blog::create([
        'title' => 'Test Blog',
    ]);

    try {
        // This will trigger registerMediaConversions, which calls addResponsive16x9Conversion
        // which eventually calls getimagesize() on the file.
        $blog->addMedia($dummyPath)->toMediaCollection('blog-main');
        
        // If we reached here, it didn't crash
        expect(true)->toBeTrue();
    } finally {
        if (File::exists($dummyPath)) {
            File::delete($dummyPath);
        }
    }
});
