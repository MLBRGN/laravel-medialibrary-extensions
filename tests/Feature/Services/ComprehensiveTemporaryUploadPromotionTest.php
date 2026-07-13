<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\TemporaryUploadPromoter;
use Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

beforeEach(function () {
    $this->temporaryDisk = config('medialibrary-extensions.media_disks.temporary');
    $this->demoDisk = PackageInfrastructure::disk('demo');

    config()->set("filesystems.disks.{$this->demoDisk}", [
        'driver' => 'local',
        'root' => $this->getTempDirectory($this->demoDisk),
    ]);

    Storage::fake($this->demoDisk);
});

it('comprehensively promotes temporary uploads on the correct connection and disk', function () {
    // 1. Setup default promotion scenario (mle_test_host_app)
    $defaultPost = Blog::create(['title' => 'Default blog']);
    $defaultToken = 'token-default';
    $defaultFilename = 'default.jpg';
    Storage::disk($this->temporaryDisk)->put($defaultFilename, 'default content');

    TemporaryUpload::create([
        'disk' => $this->temporaryDisk,
        'path' => $defaultFilename,
        'name' => 'default',
        'file_name' => $defaultFilename,
        'collection_name' => 'blog-main',
        'client_token' => $defaultToken,
        'size' => 123,
    ]);

    // 2. Setup alt promotion scenario (mle_test_demo)
    // Alien exists on mle_test_demo in TestCase migrations.
    $altPost = (new Alien)->setConnection('mle_test_demo');
    $altPost->save();

    $altToken = 'token-alt';
    $altFilename = 'alt.jpg';
    $altTempDisk = $this->temporaryDisk;

    Storage::disk($altTempDisk)->put($altFilename, 'alt content');

    // We must manually insert into alt connection for the temp upload
    TemporaryUpload::on('mle_test_demo')->create([
        'disk' => $altTempDisk,
        'path' => $altFilename,
        'name' => 'alt',
        'file_name' => $altFilename,
        'collection_name' => 'alien-single-image',
        'client_token' => $altToken,
        'size' => 123,
    ]);

    // 3. Run promotion for default
    app(TemporaryUploadPromoter::class)->promoteAllForModel($defaultPost, null, $defaultToken);

    // Verify default promotion
    expect(TemporaryUpload::count())->toBe(0);
    expect(Media::count())->toBe(1);
    $defaultMedia = $defaultPost->getFirstMedia('blog-main');
    expect($defaultMedia)->not->toBeNull();
    // Default disk in tests seems to be 'public' (which is faked to point to media dir)
    expect($defaultMedia->disk)->toBe('public');
    expect(Storage::disk('public')->exists($defaultMedia->id.'/'.$defaultFilename))->toBeTrue();
    expect(Storage::disk($this->temporaryDisk)->exists($defaultFilename))->toBeFalse();

    // 4. Run promotion for alt
    app(TemporaryUploadPromoter::class)->promoteAllForModel($altPost, null, $altToken);

    // Verify alt promotion
    expect(TemporaryUpload::on('mle_test_demo')->count())->toBe(0);
    expect(Media::on('mle_test_demo')->count())->toBe(1);

    $altMedia = $altPost->getFirstMedia('alien-single-image');
    expect($altMedia)->not->toBeNull();
    expect($altMedia->getConnectionName())->toBe('mle_test_demo');
    // Alien model uses 'media_demo' disk in its collection definition,
    // but in tests faked disks usually have the same name as the config value.
    $alienDisk = PackageInfrastructure::disk('demo');
    expect($altMedia->disk)->toBe($alienDisk);

    expect(Storage::disk($alienDisk)->exists($altMedia->id.'/'.$altFilename))->toBeTrue();
    expect(Storage::disk($altTempDisk)->exists($altFilename))->toBeFalse();
})->todo('This test is not working yet');

it('verifies that media table on alt connection is populated correctly', function () {
    $altPost = (new Alien)->setConnection('mle_test_demo');
    $altPost->save();

    $altToken = 'token-alt-2';
    $altFilename = 'alt2.jpg';
    $altTempDisk = $this->temporaryDisk;

    Storage::disk($altTempDisk)->put($altFilename, 'alt content 2');

    TemporaryUpload::on('mle_test_demo')->create([
        'disk' => $altTempDisk,
        'path' => $altFilename,
        'name' => 'alt2',
        'file_name' => $altFilename,
        'collection_name' => 'alien-single-image',
        'client_token' => $altToken,
        'size' => 123,
    ]);

    app(TemporaryUploadPromoter::class)->promoteAllForModel($altPost, null, $altToken);

    // Check alt media table directly
    $mediaCount = DB::connection('mle_test_demo')->table('media')->count();
    expect($mediaCount)->toBe(1);

    $mediaRecord = DB::connection('mle_test_demo')->table('media')->first();
    expect($mediaRecord->file_name)->toBe($altFilename);
    expect((string) $mediaRecord->model_id)->toBe((string) $altPost->id);
    expect($mediaRecord->model_type)->toBe($altPost->getMorphClass());
})->todo('This test is not working yet');
