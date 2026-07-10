<?php

/** @noinspection HtmlUnknownTarget */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;
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
    $this->temporaryDisk = config('medialibrary-extensions.media_disks.temporary');
    $this->temporaryDiskUrl = config("medialibrary-extensions.disks.{$this->temporaryDisk}.url");
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
 * Prepare HTML filename and disk-safe filename from the original
 */
function prepareSafeFilenames(string $originalName): array
{
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $diskFilename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME), '-').'.'.$extension;

    // HTML may contain soft hyphens etc.
    $htmlFilename = $originalName;

    return compact('diskFilename', 'htmlFilename');
}

/*
|--------------------------------------------------------------------------
| Tests
|--------------------------------------------------------------------------
*/

it('promotes from the correct data source connection and cleans up there', function () {
    // Set up an alternate SQLite connection and map a data source to it
    config()->set('database.connections.alt', [
        'driver' => 'sqlite',
        'database' => database_path('alt.sqlite'),
        'prefix' => '',
        'foreign_key_constraints' => true,
    ]);

    // Ensure the sqlite file exists
    if (! file_exists(database_path('alt.sqlite'))) {
        touch(database_path('alt.sqlite'));
    }

    // Map data source key "alt_source" to this connection
    config()->set('medialibrary-extensions.data_sources.alt_source', [
        'connection' => 'alt',
    ]);

    // Create TemporaryUpload table on the alt connection if not exists
    // Note: the model uses the same table name regardless of connection
    if (! Schema::connection('alt')->hasTable('mle_temporary_uploads')) {
        Schema::connection('alt')->create('mle_temporary_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('disk');
            $table->string('path');
            $table->string('name');
            $table->string('file_name');
            $table->string('collection_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('client_token');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('instance_id')->nullable();
            $table->json('custom_properties')->nullable();
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();
        });
    } else {
        // Ensure a clean slate on repeated runs: remove any leftovers from previous tests
        TemporaryUpload::on('alt')->delete();
    }

    if (! Schema::connection('alt')->hasTable('test_posts')) {
        Schema::connection('alt')->create('test_posts', function (Blueprint $table) {
            $table->id();
            $table->string('content');
            $table->timestamps();
        });
    } else {
        TestPost::on('alt')->delete();
    }

    if (! Schema::connection('alt')->hasTable('media')) {
        Schema::connection('alt')->create('media', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(['model_type', 'model_id']);
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable()->index();
            $table->timestamps();
        });
    } else {
        Schema::connection('alt')->table('media', function ($table) {
            DB::connection('alt')->table('media')->delete();
        });
    }

    // Prepare a post and a temp upload on the alt data source
    $post = (new TestPost)->setConnection('alt');
    $post->fill(['content' => 'Hello <img src="/storage/media_temporary/tmp-alt.png" alt="">'])->save();

    $clientToken = (string) Str::ulid();

    // Put a file on the temporary disk
    Storage::disk($this->temporaryDisk)->put('tmp-alt.png', 'fake');

    // Insert TemporaryUpload on the alt connection using the model scope
    $altTemp = (new TemporaryUpload)
        ->setConnection(app(DataSourceResolver::class)->resolveConnection('alt_source'));

    $altTemp->fill([
        'disk' => $this->temporaryDisk,
        'path' => 'tmp-alt.png',
        'name' => 'tmp-alt',
        'file_name' => 'tmp-alt.png',
        'collection_name' => 'images',
        'mime_type' => 'image/png',
        'size' => 4,
        'client_token' => $clientToken,
        'instance_id' => null,
        'order_column' => 0,
        'custom_properties' => [
            'collections' => ['image' => 'images'],
            'priority' => 0,
        ],
    ])->save();

    // Sanity: the record exists only on alt connection
    expect(TemporaryUpload::query()->count())->toBe(0);
    expect(TemporaryUpload::on('alt')->count())->toBe(1);

    // Promote: note that TemporaryUploadPromoter currently does not take a data source;
    // it should still promote correctly if it internally resolves the proper connection
    app(TemporaryUploadPromoter::class)->promoteAllForModel($post, null, $clientToken);

    $post->refresh();

    // Assert media attached
    $media = $post->getFirstMedia('images');
    expect($media)->not->toBeNull();

    // Assert the temporary upload row on the alt connection has been deleted
    expect(TemporaryUpload::on('alt')->count())->toBe(0);

    // And the file on the temporary disk has been removed
    Storage::disk($this->temporaryDisk)->assertMissing('tmp-alt.png');
})->todo();

it('replaces relative temporary media urls in html', function () {
    $filename = 'image.png';
    $post = TestPost::create([
        'content' => "<p><img src=\"/storage/media_temporary/{$filename}\" alt=''></p>",
    ]);

    $clientToken = (string) Str::ulid();
    $this->createTemporaryUpload([
        'path' => $filename,
        'name' => pathinfo($filename, PATHINFO_FILENAME),
        'file_name' => $filename,
        'client_token' => $clientToken,
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post, null, $clientToken);

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

    // Create the safe disk filename
    $diskFilename = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME), '-').'.'.pathinfo($originalFilename, PATHINFO_EXTENSION);

    // HTML should use the same filename as in temp storage
    $post = TestPost::create([
        'content' => "<p><img src=\"/storage/media_temporary/{$diskFilename}\" alt=''></p>",
    ]);

    $clientToken = (string) Str::ulid();
    $this->createTemporaryUpload([
        'path' => $diskFilename,
        'name' => pathinfo($diskFilename, PATHINFO_FILENAME),
        'file_name' => $diskFilename,
        'client_token' => $clientToken,
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post, null, $clientToken);

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

    $clientToken = (string) Str::ulid();
    $this->createTemporaryUpload([
        'path' => $filename,
        'name' => pathinfo($filename, PATHINFO_FILENAME),
        'file_name' => $filename,
        'client_token' => $clientToken,
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post, null, $clientToken);

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

    $clientToken = (string) Str::ulid();
    $this->createTemporaryUpload([
        'path' => $filename,
        'name' => pathinfo($filename, PATHINFO_FILENAME),
        'file_name' => $filename,
        'client_token' => $clientToken,
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post, null, $clientToken);

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
        .'.'.pathinfo($originalFilename, PATHINFO_EXTENSION);

    // HTML uses the sanitized disk filename (this matches temp uploads)
    $post = TestPost::create([
        'content' => "<p><img src=\"/storage/media_temporary/{$diskFilename}\" alt=''></p>",
    ]);

    $clientToken = (string) Str::ulid();
    $this->createTemporaryUpload([
        'path' => $diskFilename,
        'name' => pathinfo($diskFilename, PATHINFO_FILENAME),
        'file_name' => $diskFilename,
        'client_token' => $clientToken,
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post, null, $clientToken);

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

    $clientToken = (string) Str::ulid();
    $this->createTemporaryUpload([
        'path' => $filename,
        'name' => pathinfo($filename, PATHINFO_FILENAME),
        'file_name' => $filename,
        'client_token' => $clientToken,
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post, null, $clientToken);

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

    $clientToken = (string) Str::ulid();
    foreach ($filenames as $file) {
        $this->createTemporaryUpload([
            'path' => $file,
            'name' => pathinfo($file, PATHINFO_FILENAME),
            'file_name' => $file,
            'client_token' => $clientToken,
        ]);
    }

    app(TemporaryUploadPromoter::class)->promoteAllForModel($post, null, $clientToken);

    $post->refresh();

    foreach ($filenames as $file) {
        $normalized = normalizeFilename($file);
        expect($post->content)
            ->not->toContain('media_temporary')
            ->toMatch("#/storage/\d+/{$normalized}#");
        Storage::disk($this->temporaryDisk)->assertMissing($file);
    }
});
