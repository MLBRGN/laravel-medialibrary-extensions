<?php /** @noinspection HtmlUnknownTarget */

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\TemporaryUploadPromoter;
use Mlbrgn\MediaLibraryExtensions\Tests\Support\Models\TestPost;

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

/**
 * Prepare HTML filename and disk-safe filename from original
 */
function prepareSafeFilenames(string $originalName): array
{
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $diskFilename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME), '-') . '.' . $extension;

    // HTML may contain soft hyphens etc.
    $htmlFilename = $originalName;

    return compact('diskFilename', 'htmlFilename');
}

/*
|--------------------------------------------------------------------------
| Tests
|--------------------------------------------------------------------------
*/

it('replaces relative temporary media urls in html', function () {
    $filename = 'image.png';
    $post = TestPost::create([
        'content' => "<p><img src=\"/storage/media_temporary/{$filename}\" alt=''></p>",
    ]);

    $this->createTemporaryUpload([
        'path' => $filename,
        'name' => pathinfo($filename, PATHINFO_FILENAME),
        'file_name' => $filename,
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    $post->refresh();
    $media = $post->getFirstMedia();
    $normalized = normalizeFilename($filename);

    expect($post->content)
        ->not->toContain('media_temporary')
        ->toContain($media->getUrl())
        ->toMatch("#/storage/\d+/{$normalized}#");

    Storage::disk($this->temporaryDisk)->assertMissing($filename);
});

it('replaces relative temporary media urls with unicode / soft hyphen filenames', function () {
    $originalFilename = "Screen\u{00AD}Shot 2026-01-17.png";

    // Create safe disk filename
    $diskFilename = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME), '-') . '.' . pathinfo($originalFilename, PATHINFO_EXTENSION);

    // HTML should use the same filename as in temp storage
    $post = TestPost::create([
        'content' => "<p><img src=\"/storage/media_temporary/{$diskFilename}\" alt=''></p>",
    ]);

    $this->createTemporaryUpload([
        'path' => $diskFilename,
        'name' => pathinfo($diskFilename, PATHINFO_FILENAME),
        'file_name' => $diskFilename,
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    $post->refresh();
    $media = $post->getFirstMedia();

    $normalized = normalizeFilename($diskFilename);

    expect($post->content)
        ->not->toContain("/storage/media_temporary/{$diskFilename}")
        ->toContain($media->getUrl());

    expect($media->file_name)->toContain($normalized);
    Storage::disk($this->temporaryDisk)->assertMissing($diskFilename);
});


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
    $media = $post->getFirstMedia();
    $normalized = normalizeFilename($filename);

    expect($post->content)
        ->not->toContain('media_temporary')
        ->toContain($media->getUrl())
        ->toMatch("#/storage/\d+/{$normalized}#");

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
        ->and($post->content)->toMatch("#/storage/\d+/{$normalized}#");

    Storage::disk($this->temporaryDisk)->assertMissing($filename);
});

it('replaces temporary uploads with Unicode / unsafe filenames in HTML', function () {
    $originalFilename = "Émóji 💾 file\u{00AD} name.png";

    // Safe disk filename
    $diskFilename = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME), '-')
        . '.' . pathinfo($originalFilename, PATHINFO_EXTENSION);

    // HTML uses the sanitized disk filename (this matches temp uploads)
    $post = TestPost::create([
        'content' => "<p><img src=\"/storage/media_temporary/{$diskFilename}\" alt=''></p>",
    ]);

    $this->createTemporaryUpload([
        'path' => $diskFilename,
        'name' => pathinfo($diskFilename, PATHINFO_FILENAME),
        'file_name' => $diskFilename,
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    $post->refresh();
    $media = $post->getFirstMedia();

    $normalized = normalizeFilename($diskFilename);

    expect($post->content)
        ->not->toContain("/storage/media_temporary/{$diskFilename}")
        ->toContain($media->getUrl());

    expect($media->file_name)->toContain($normalized);
    Storage::disk($this->temporaryDisk)->assertMissing($diskFilename);
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
            ->toMatch("#/storage/\d+/{$normalized}#");
        Storage::disk($this->temporaryDisk)->assertMissing($file);
    }
});
