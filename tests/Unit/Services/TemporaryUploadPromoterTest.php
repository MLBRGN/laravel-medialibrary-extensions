<?php /** @noinspection HtmlUnknownTarget */

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\TemporaryUploadPromoter;
use Mlbrgn\MediaLibraryExtensions\Tests\Support\Models\TestPost;
use Spatie\MediaLibrary\Conversions\FileManipulator;

beforeEach(function () {
    Storage::fake('public');

    session()->start();

    // Test model table
    Schema::create('test_posts', function (Blueprint $table) {
        $table->id();
        $table->text('content')->nullable();
        $table->timestamps();
    });

    // Get your package’s temporary disk dynamically
    $this->temporaryDisk = config('media-library-extensions.media_disks.temporary');
    $this->temporaryDiskUrl = config("media-library-extensions.disks.{$this->temporaryDisk}.url");
});

afterEach(function () {
    Schema::dropIfExists('test_posts');
});

/**
 * Helper to normalize filenames for regex matching
 */
function normalizeFilename(string $filename): string
{
    // Remove soft hyphens, zero-width spaces, BOM
    return preg_replace('/[\x{00AD}\x{200B}-\x{200D}\x{FEFF}]+/u', '', $filename);
}

/*
|--------------------------------------------------------------------------
| Tests
|--------------------------------------------------------------------------
*/

it('replaces relative temporary media urls in html', function () {

//    TestPost::unsetEventDispatcher();// don't listen to model create event (otherwise Promoter gets called twice)
//    Event::fake();

    $filename = 'image.png';

    $post = TestPost::create([
        'content' => "<h1>hello world</h1><p>test</p><p><img src=\"/storage/media_temporary/{$filename}\" alt=''></p>",
    ]);

    $temporaryUpload = $this->createTemporaryUpload([
        'path' => $filename,
        'name' => pathinfo($filename, PATHINFO_FILENAME),
        'file_name' => $filename,
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    $post->refresh();

    $normalized = normalizeFilename($filename);

    $media = $post->getFirstMedia();

    expect($post->content)
        ->toContain($media->getUrl());
    // Make sure old temp URL is gone
//    expect($post->content)
//        ->not->toContain("/storage/media_temporary/{$filename}") // original URL gone
//        ->toMatch("#/storage/media/\d+/{$normalized}#"); // new URL present

    Storage::disk($this->temporaryDisk)->assertMissing($filename);
})->only();

it('replaces relative temporary media urls in html with soft hyphen', function () {
    // Soft hyphen in HTML content (invisible character)
    $htmlFilename = "image\u{00AD} with some junk.png";

    // Safe filename for disk
    $diskFilename = str_replace("\u{00AD}", '-', $htmlFilename);

    // Create post with temporary media URL
    $post = TestPost::create([
        'content' => "<p><img src=\"/storage/media_temporary/{$htmlFilename}\" alt=''></p>",
    ]);

    // Create a temporary upload with SAFE disk filename
    $temporaryUpload = $this->createTemporaryUpload([
        'path' => $diskFilename,
        'name' => pathinfo($diskFilename, PATHINFO_FILENAME),
        'file_name' => $diskFilename,
    ]);

    // Promote temporary media to permanent storage
    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    $post->refresh();

    $media = $post->getFirstMedia();

    // The HTML content now contains the promoted media URL
    expect($post->content)->toContain($media->getUrl());

    // Original temp file (safe name) should be deleted
    Storage::disk($this->temporaryDisk)->assertMissing($diskFilename);

    // Optionally, assert that normalized filename (soft hyphen replaced) is in media filename
    $normalized = normalizeFilename($htmlFilename);
    expect($media->file_name)->toContain($normalized);
});

it('promotes temporary media and replaces soft hyphen in HTML', function () {
    // Simulate a macOS screenshot filename with soft hyphen
    $filename = "Screen\u{00AD}Shot 2026-01-17 at 12.34.56 PM.png";

    $sanitized = Str::slug(pathinfo($filename, PATHINFO_FILENAME), '-') . '.' . pathinfo($filename, PATHINFO_EXTENSION);

    dump($filename);
    dump($sanitized);
    // Prepare filenames
    $files = $this->prepareFilenameForTest($filename);
    $htmlFilename = $files['html'];
    $diskFilename = $files['disk'];

    // Create post with temporary URL
    $post = TestPost::create([
        'content' => "<p><img src=\"/storage/media_temporary/{$htmlFilename}\" alt=''></p>",
    ]);

    // Create temporary upload with SAFE disk filename
    $temporaryUpload = $this->createTemporaryUpload([
        'path' => $diskFilename,
        'name' => pathinfo($diskFilename, PATHINFO_FILENAME),
        'file_name' => $diskFilename,
    ]);

    // Promote temporary media
    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    $post->refresh();
    $media = $post->getFirstMedia();

    // HTML content contains new media URL
    expect($post->content)->toContain($media->getUrl());

    // Original temp URL (with soft hyphen) is gone
    expect($post->content)->not->toContain("/storage/media_temporary/{$htmlFilename}");

    // Normalized filename (soft hyphen replaced) is used in the media
    $normalized = normalizeFilename($htmlFilename);
    expect($media->file_name)->toContain($normalized);

    // Temp file is deleted
    Storage::disk($this->temporaryDisk)->assertMissing($diskFilename);
})->only();



it('replaces absolute temporary media urls in html', function () {
    $filename = 'image.png';
    $post = TestPost::create([
        'content' => "<img src=\"{$this->temporaryDiskUrl}/{$filename}\" alt=''>",
    ]);

    $this->createTemporaryUpload([
        'path' => $filename,
        'name' => pathinfo($filename, PATHINFO_FILENAME),
        'file_name' => $filename,
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    $post->refresh();

    $normalized = normalizeFilename($filename);

    expect($post->content)
        ->not->toContain('media_temporary')
        ->toMatch("#/storage/media/\d+/{$normalized}#");

    Storage::disk($this->temporaryDisk)->assertMissing($filename);
});

it('replaces mixed absolute and relative urls', function () {
    $filename = 'image.png';
    $post = TestPost::create([
        'content' => "
            <img src=\"/storage/media_temporary/{$filename}\" alt=''>
            <img src=\"{$this->temporaryDiskUrl}/{$filename}\" alt=''>
        ",
    ]);

    $this->createTemporaryUpload([
        'path' => $filename,
        'name' => pathinfo($filename, PATHINFO_FILENAME),
        'file_name' => $filename,
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    $post->refresh();

    $normalized = normalizeFilename($filename);

    expect(substr_count($post->content, 'media_temporary'))->toBe(0)
        ->and(substr_count($post->content, $this->temporaryDiskUrl))->toBe(0)
        ->and($post->content)->toMatch("#/storage/media/\d+/{$normalized}#");

    Storage::disk($this->temporaryDisk)->assertMissing($filename);
});

it('removes temporary upload file and database record', function () {
    $filename = 'image.png';
    $post = TestPost::create([
        'content' => "<img src=\"/storage/media_temporary/{$filename}\" alt=''>",
    ]);

    $this->createTemporaryUpload([
        'path' => $filename,
        'name' => pathinfo($filename, PATHINFO_FILENAME),
        'file_name' => $filename,
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    expect(TemporaryUpload::count())->toBe(0);
    Storage::disk($this->temporaryDisk)->assertMissing($filename);
});

it('does not touch model when no temp urls exist', function () {
    $post = TestPost::create([
        'content' => '<p>No images here</p>',
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    expect($post->wasChanged())->toBeFalse();
});

it('replaces multiple temporary uploads in one html field', function () {
    $filenames = ['a.png', 'b.png'];
    $post = TestPost::create([
        'content' => "
            <img src=\"/storage/media_temporary/{$filenames[0]}\" alt=''>
            <img src=\"/storage/media_temporary/{$filenames[1]}\" alt=''>
        ",
    ]);

    foreach ($filenames as $file) {
        $this->createTemporaryUpload([
            'path' => $file,
            'name' => pathinfo($file, PATHINFO_FILENAME),
            'file_name' => $file,
        ]);
    }

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    $post->refresh();

    foreach ($filenames as $file) {
        $normalized = normalizeFilename($file);
        expect($post->content)
            ->not->toContain('media_temporary')
            ->toMatch("#/storage/media/\d+/{$normalized}#");

        Storage::disk($this->temporaryDisk)->assertMissing($file);
    }
});
