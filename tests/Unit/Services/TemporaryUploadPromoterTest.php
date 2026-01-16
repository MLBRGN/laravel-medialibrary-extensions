<?php /** @noinspection HtmlUnknownTarget */

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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

/*
|--------------------------------------------------------------------------
| Tests
|--------------------------------------------------------------------------
*/

it('replaces relative temporary media urls in html', function () {
    $post = TestPost::create([
        'content' => '<img src="/storage/media_temporary/image.png" alt="">',
    ]);

    $this->createTemporaryUpload([
        'path' => 'image.png',
        'name' => 'image',
        'file_name' => 'image.png',
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    $post->refresh();

    expect($post->content)
        ->not->toContain('media_temporary')
        ->toMatch('#/storage/\d+/image\.png#');

    Storage::disk($this->temporaryDisk)->assertMissing('image.png');
});

it('replaces absolute temporary media urls in html', function () {
    $post = TestPost::create([
        'content' => "<img src=\"{$this->temporaryDiskUrl}/image.png\" alt=''>",
    ]);

    $this->createTemporaryUpload([
        'path' => 'image.png',
        'name' => 'image',
        'file_name' => 'image.png',
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    $post->refresh();

    expect($post->content)
        ->not->toContain($this->temporaryDiskUrl)
        ->toMatch('#/storage/\d+/image\.png#');

    Storage::disk($this->temporaryDisk)->assertMissing('image.png');
});

it('replaces mixed absolute and relative urls', function () {
    $post = TestPost::create([
        'content' => "
            <img src=\"/storage/media_temporary/image.png\" alt=\"\">
            <img src=\"{$this->temporaryDiskUrl}/image.png\" alt=\"\">
        ",
    ]);

    $this->createTemporaryUpload([
        'path' => 'image.png',
        'name' => 'image',
        'file_name' => 'image.png',
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    $post->refresh();

    expect(substr_count($post->content, $this->temporaryDiskUrl))->toBe(0)
        ->and(substr_count($post->content, 'media_temporary'))->toBe(0)
        ->and($post->content)->toMatch('#/storage/\d+/image\.png#');

    Storage::disk($this->temporaryDisk)->assertMissing('image.png');
});

it('removes temporary upload file and database record', function () {
    $post = TestPost::create([
        'content' => '<img src="/storage/media_temporary/image.png" alt="">',
    ]);

    $this->createTemporaryUpload([
        'path' => 'image.png',
        'name' => 'image',
        'file_name' => 'image.png',
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    expect(TemporaryUpload::count())->toBe(0);

    Storage::disk($this->temporaryDisk)->assertMissing('image.png');
});

it('does not touch model when no temp urls exist', function () {
    $post = TestPost::create([
        'content' => '<p>No images here</p>',
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    expect($post->wasChanged())->toBeFalse();
});

it('replaces multiple temporary uploads in one html field', function () {
    $post = TestPost::create([
        'content' => "
            <img src=\"/storage/media_temporary/a.png\" alt=\"\">
            <img src=\"/storage/media_temporary/b.png\" alt=\"\">
        ",
    ]);

    $this->createTemporaryUpload([
        'path' => 'a.png',
        'name' => 'a',
        'file_name' => 'a.png',
    ]);

    $this->createTemporaryUpload([
        'path' => 'b.png',
        'name' => 'b',
        'file_name' => 'b.png',
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    $post->refresh();

    expect($post->content)
        ->not->toContain($this->temporaryDiskUrl)
        ->toMatch('#/storage/\d+/a\.png#')
        ->toMatch('#/storage/\d+/b\.png#');

    Storage::disk($this->temporaryDisk)->assertMissing('a.png');
    Storage::disk($this->temporaryDisk)->assertMissing('b.png');
});
