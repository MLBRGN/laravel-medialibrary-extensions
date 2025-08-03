<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    Storage::fake('media');
    Session::start();
    Log::spy();
});

it('attaches temporary media on model creation', function () {
    $upload = TemporaryUpload::create([
        'disk' => 'media',
        'path' => 'uploads/temp.jpg',
        'original_filename' => 'temp.jpg',
        'collection_name' => 'test',
        'extra_properties' => ['image_collection' => 'images'],
        'session_id' => session()->getId(),
    ]);

    // Put dummy file to fake disk
    Storage::disk('media')->put('uploads/temp.jpg', 'dummy content');

    $model = Blog::create(['title' => 'Testing']);

    $media = $model->getMedia('images')->first();

    expect($media)->not->toBeNull()
        ->and(Storage::disk('media')->exists('uploads/temp.jpg'))->toBeFalse()
        ->and(TemporaryUpload::count())->toBe(0);
})->todo();

it('skips attachment if no collection is defined', function () {
    TemporaryUpload::create([
        'disk' => 'media',
        'path' => 'uploads/skip.jpg',
        'original_filename' => 'skip.jpg',
        'collection_name' => 'test',
        'extra_properties' => [], // no collection info
        'session_id' => session()->getId(),
    ]);

    Storage::disk('media')->put('uploads/skip.jpg', 'dummy');

    expect(fn () => Blog::create(['title' => 'Invalid']))->toThrow(Exception::class, 'No image or document collection provided');

    Storage::disk('media')->assertExists('uploads/skip.jpg');
    expect(TemporaryUpload::count())->toBe(1);
});

it('logs error when safeAddMedia fails', function () {
    $model = Mockery::mock(Blog::class)->makePartial();
    $model->shouldReceive('addMediaFromDisk')->andThrow(new Exception('Test failure'));

    $refMethod = new ReflectionMethod($model, 'safeAddMedia');
    $refMethod->setAccessible(true);

    $refMethod->invoke($model, $model, 'fake-path.jpg', 'media', 'file.jpg', 'images');

    Log::shouldHaveReceived('error')
        ->withArgs(fn ($message, $context) => str_contains($message, 'Failed to attach media') &&
            $context['path'] === 'fake-path.jpg'
        );
});
