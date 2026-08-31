<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\TemporaryUploadPromoter;
use Mlbrgn\MediaLibraryExtensions\Tests\Support\Models\TestPost;

beforeEach(function () {
    $this->temporaryDisk = config('medialibrary-extensions.media_disks.temporary') ?: 'public';
    Storage::fake($this->temporaryDisk);

    session()->start();

    // Test model table
    if (!Schema::hasTable('test_posts')) {
        Schema::create('test_posts', function (Blueprint $table) {
            $table->id();
            $table->text('content')->nullable();
            $table->timestamps();
        });
    }
});

afterEach(function () {
    Schema::dropIfExists('test_posts');
});

it('promotes media from multiple instance IDs provided as an array', function () {
    $clientToken = (string) Str::ulid();
    $instanceIds = ['instance-1', 'instance-2'];

    $temp1 = $this->createTemporaryUpload([
        'disk' => $this->temporaryDisk,
        'path' => 'file1.png',
        'file_name' => 'file1.png',
        'client_token' => $clientToken,
        'instance_id' => 'instance-1',
    ]);

    $temp2 = $this->createTemporaryUpload([
        'disk' => $this->temporaryDisk,
        'path' => 'file2.png',
        'file_name' => 'file2.png',
        'client_token' => $clientToken,
        'instance_id' => 'instance-2',
    ]);

    $post = TestPost::create(['content' => 'Test Post']);

    // Promote both instances
    app(TemporaryUploadPromoter::class)->promoteAllForModel($post, $instanceIds, $clientToken);

    $post->refresh();
    
    // Verify both media are attached
    expect($post->getMedia()->count())->toBe(2);
    expect($post->getMedia()->pluck('file_name')->toArray())->toContain('file1.png', 'file2.png');

    // Verify both temporary records are gone
    expect(TemporaryUpload::count())->toBe(0);
    
    // Verify files are gone from temp disk
    Storage::disk($this->temporaryDisk)->assertMissing('file1.png');
    Storage::disk($this->temporaryDisk)->assertMissing('file2.png');
});

it('only promotes media from the specified instance IDs in the array', function () {
    $clientToken = (string) Str::ulid();
    
    $temp1 = $this->createTemporaryUpload([
        'disk' => $this->temporaryDisk,
        'path' => 'file1.png',
        'file_name' => 'file1.png',
        'client_token' => $clientToken,
        'instance_id' => 'instance-1',
    ]);

    $temp2 = $this->createTemporaryUpload([
        'disk' => $this->temporaryDisk,
        'path' => 'file2.png',
        'file_name' => 'file2.png',
        'client_token' => $clientToken,
        'instance_id' => 'instance-2',
    ]);

    $post = TestPost::create(['content' => 'Test Post']);

    // Promote only instance-1
    app(TemporaryUploadPromoter::class)->promoteAllForModel($post, ['instance-1'], $clientToken);

    $post->refresh();
    
    // Verify only file1 is attached
    expect($post->getMedia()->count())->toBe(1);
    expect($post->getMedia()->first()->file_name)->toBe('file1.png');

    // Verify temp2 still exists, temp1 is gone
    expect(TemporaryUpload::count())->toBe(1);
    expect(TemporaryUpload::first()->instance_id)->toBe('instance-2');
    
    // Verify file1 is gone, file2 remains
    Storage::disk($this->temporaryDisk)->assertMissing('file1.png');
    Storage::disk($this->temporaryDisk)->assertExists('file2.png');
});

it('promotes multiple instances from request input mle_instance_ids', function () {
    $clientToken = (string) Str::ulid();
    $instanceIds = ['instance-a', 'instance-b'];

    $this->createTemporaryUpload([
        'disk' => $this->temporaryDisk,
        'path' => 'a.png',
        'file_name' => 'a.png',
        'client_token' => $clientToken,
        'instance_id' => 'instance-a',
    ]);

    $this->createTemporaryUpload([
        'disk' => $this->temporaryDisk,
        'path' => 'b.png',
        'file_name' => 'b.png',
        'client_token' => $clientToken,
        'instance_id' => 'instance-b',
    ]);

    $post = TestPost::create(['content' => 'Test Post']);

    // Mock request data
    request()->merge([
        'client_token' => $clientToken,
        'mle_instance_ids' => $instanceIds,
    ]);

    // Call without explicit instanceId to use request input
    app(TemporaryUploadPromoter::class)->promoteAllForModel($post);

    $post->refresh();
    
    expect($post->getMedia()->count())->toBe(2);
    expect(TemporaryUpload::count())->toBe(0);
});

it('promotes multiple instances automatically via InteractsWithMediaExtended trait on created event', function () {
    $clientToken = (string) Str::ulid();
    $instanceIds = ['instance-x', 'instance-y'];

    $this->createTemporaryUpload([
        'disk' => $this->temporaryDisk,
        'path' => 'x.png',
        'file_name' => 'x.png',
        'client_token' => $clientToken,
        'instance_id' => 'instance-x',
    ]);

    $this->createTemporaryUpload([
        'disk' => $this->temporaryDisk,
        'path' => 'y.png',
        'file_name' => 'y.png',
        'client_token' => $clientToken,
        'instance_id' => 'instance-y',
    ]);

    // Mock request data
    request()->merge([
        'client_token' => $clientToken,
        'mle_instance_ids' => $instanceIds,
    ]);

    // Creating the post should trigger promotion via the trait
    $post = TestPost::create(['content' => 'Auto Promotion Test']);

    $post->refresh();
    
    expect($post->getMedia()->count())->toBe(2);
    expect($post->getMedia()->pluck('file_name')->toArray())->toContain('x.png', 'y.png');
    expect(TemporaryUpload::count())->toBe(0);
});
